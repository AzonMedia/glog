<?php
/**
 * Restarts the Swoole server (by killing the start_server.php file and executing it again)
 * when there is a change in files in the project folder
 * @author julia@azonmedia.com
 * @created Sep 12, 2019
 * @see http://gitlab.guzaba.org/root/guzaba-framework-2/issues/14
 */ 
declare(strict_types=1);

namespace Azonmedia\Glog;

use Azonmedia\Di\Container;
use Azonmedia\Glog\Application\Glog;
use Azonmedia\Registry\Interfaces\RegistryBackendInterface;
use Azonmedia\Registry\Registry;
use Azonmedia\Registry\RegistryBackendEnv;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Database\ConnectionProviders\Pool;
use Guzaba2\Kernel\Kernel;
use Guzaba2\Authorization\AuthorizationMiddleware;
use Guzaba2\Authorization\FilteringMiddleware;
use Guzaba2\Mvc\ExecutorMiddleware;
use Azonmedia\Glog\Middleware\ServingMiddleware;
use Guzaba2\Registry\Interfaces\RegistryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$autoload_path = realpath(__DIR__ . '/../../vendor/autoload.php');
require_once($autoload_path);

/**
 * Local logger function
 * 
 * @param string $file_name - the log filename, without extension
 * @param string $text - the text that will be written in the log file
 */ 
$my_logger = function($file_name, $text) {
    $logfile = realpath(dirname(getcwd()));
    $logfile.= '/logs/' . $file_name . '.txt';
    $ret = (bool) file_put_contents($logfile, $text.PHP_EOL.PHP_EOL.PHP_EOL, FILE_APPEND);
};


//settings needed for proc_open function
$working_dir = realpath(dirname(getcwd()));

$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("file", "{$working_dir}/logs/continuous_server_errors.txt", "a") // stderr is a file to write to
);

$cwd = $working_dir .'/bin';

$process = '';

//array that will hold all child process ids of the main process, generated by $get_process_children()
$process_ids = array();

/**
 * Recursive function to get all child process ids of the main process  
 * @param int $search_pid - main process id whose children will be searched
 */ 
$get_process_children = function ($search_pid) use (&$get_process_children, &$process_ids, $my_logger) {

    // $my_logger('children',"start") ;

    if (empty($search_pid)) {
        return false;
    }

    $ppid_result = shell_exec("ps -o pid --no-heading --ppid $search_pid");
    // $my_logger('children',"ps -o pid --no-heading --ppid $search_pid") ;
    if (!empty($ppid_result)) {
        $pids = preg_split('/\s+/', $ppid_result);
        foreach($pids as $pid) {
            
            // $my_logger('children',$pid) ;
            if(is_numeric($pid)) {
                $process_ids[] = $pid;
                $get_process_children($pid);
            }
        }
    } else {
        return true;
    }
};


/**
 * Checks if there is a running process and restarts it
 */ 
$main_process = function() use (&$process, &$pipes, &$process_ids, $descriptorspec, $cwd, $my_logger, $get_process_children) {

    //if there is an already running process, stop it
    if (is_resource($process)) {
        // $my_logger('process', print_r([$process], true)) ;
        $status = proc_get_status($process);
        if ($status['running'] == true) { 
            
            // $my_logger('proc_close', "start");
            
            //close all pipes
            fclose($pipes[0]); //stdin
            fclose($pipes[1]); //stdout
            
            //get the pid of the process that has to be killed
            $ppid = $status['pid'];
            
            //find children of the current process
            $get_process_children($ppid);
            
            // $my_logger('children_final', print_r($process_ids, true)) ;

            if (is_array($process_ids)) {
                //reverse the array so we start with the latest child
                $process_children = array_reverse($process_ids);
                
                // $my_logger('children_final', print_r($process_children, true)) ;
                
                foreach ($process_children as $unwanted_child) {
                    $result = posix_kill((int)$unwanted_child, 15);
                    // $my_logger('children_killed', print_r([$unwanted_child, $result], true)) ;
                }
                //unset the array so it's ready for the next round
                $process_ids = array();
            }
            //terminate the main process
            proc_terminate($process);
            echo "Restarting Swoole server. \n";
            // $my_logger('proc_close', print_r([proc_get_status($process)], true));
        }
        //the waiting time is needed because currently the main process is not killed immediately, it is turned into a defunct process and it takes some time for it to die
        sleep(5);
    }

    //start the server asynchronously
    $process = proc_open('exec php start_server.php', $descriptorspec, $pipes, $cwd);

    if (is_resource($process)) {
        // $my_logger('proc_open', print_r(['start', proc_get_status($process)], true));
        
        stream_set_blocking($pipes[0], FALSE);
        stream_set_blocking($pipes[1], FALSE);
    }
};


//initial start of the swoole server
$main_process();

$ino_inst = inotify_init();

stream_set_blocking($ino_inst, FALSE);

$app_directory = realpath(dirname(getcwd()) . '/..');

$rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($app_directory));

$supported_events = IN_DELETE | IN_MODIFY;
$folders = array(); 
$inotify_inst = array();
$count = 0;
foreach ($rii as $pathname => $fileinfo) {
    if ($fileinfo->isDir()){ 
        
        //git and logs folders are excluded
        $pos = strpos($pathname, '.git');
        if ($pos === false) {
            $pos = strpos($pathname, 'logs');
            if ($pos === false) {
                $folders[] = realpath($fileinfo->getPathname()); 
                $inotify_inst[] = inotify_add_watch($ino_inst, realpath($fileinfo->getPathname()), $supported_events);
            } else {
                //$my_logger('rejected_logs', $pathname);
            }
        } else {
            //$my_logger('rejected_git', $pathname);
        }
    }
}

//$my_logger('inotify_count', print_r([count($inotify_inst), $folders], true));

while(true){
  $events = inotify_read($ino_inst);

    if ($events) {
        // $my_logger('events', print_r($events, true));
        $main_process();
    }

    //printing output from main_process file
    if (is_resource($process)) {
        while ($s = fgets($pipes[1])) {
            print $s;
            flush();
        }
    }

    usleep(50000);
}

foreach ($inotify_inst as $item) {
    inotify_rm_watch($ino_inst, $item);
}

// close our inotify instance
fclose($ino_inst);


