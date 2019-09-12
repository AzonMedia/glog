<?php

namespace Azonmedia\Glog\Home\Controllers;

use Guzaba2\Mvc\Controller;
use Guzaba2\Http\UploadedFile;
use Guzaba2\Coroutine\Coroutine;
use Psr\Http\Message\ResponseInterface;
use Guzaba2\Translator\Translator as t;
use Azonmedia\Glog\Application\MysqlConnection;
use Guzaba2\Database\ConnectionFactory;

class Home extends Controller
{

    protected const CONFIG_DEFAULTS = [
        'services'      => [
            'ConnectionFactory'
        ],
        'main_table'    => 'some_table',//defines the storage key
    ];

    protected const CONFIG_RUNTIME = [];

    public function view() : ResponseInterface
    {
        \Swoole\Runtime::enableCoroutine(true, SWOOLE_HOOK_ALL ^ SWOOLE_HOOK_FILE);

        $Response = parent::get_structured_ok_response();
        $struct =& $Response->getBody()->getStructure();
        $struct['message'] = sprintf(t::_('This is the home page'));
        
        //$log_entry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(0);
        //$log_entry->log_entry_content = 'content';
        //$log_entry->save();
        
        return $Response;
    }

    /**
     * Test Mysql Ping
     */
    // private function testMysql()
    // {
    //     $Connection = self::ConnectionFactory()->get_connection(MysqlConnection::class, $CR1);

    //     $startF = microtime(true);

    //     $query = "SELECT * FROM " . self::CONFIG_RUNTIME['main_table'];

    //     $Statement = $Connection->prepare($query);
    //     $Statement->execute();
    //     $data = $Statement->fetchAll();

    //     $endF = microtime(true);

    //     echo "executed in " . ($endF - $startF) . " s \n";

    //     \co::sleep(4);
    // }

    /**
     * Test MongoDB
     */
    // private function testMongoDB()
    // {
    //     $F = function(){
    //         echo "\nstart\n";
    //         // \co::sleep(2);

    //         $startF = microtime(true);

    //         $mongo = new \MongoDB\Client('mongodb://usc_dbuser:5xqUV78qRL@192.168.0.95:27017/uscoachways?sockettimeoutms=1200000');
    //         $collection = $mongo->selectCollection('uscoachways', 'guzaba_executions');

    //         $result = $collection->findOne(['$where' => "this.session_id == 'c4bbca7672ae8dd037c16fc0c4651c75'"]);

    //         print_r($result);

    //         $endF = microtime(true);

    //         echo "end\nexecuted in " . ($endF - $startF) . " s \n";
    //     };

    //     $start = microtime(true);

    //     $co_id_1 = Coroutine::create($F);
    //     echo "co_id_1: {$co_id_1}\n";
    //     $co_id_2 = Coroutine::create($F);
    //     echo "co_id_2: {$co_id_2}\n";

    //     $end = microtime(true);

    //     echo "\nTotal " . ($end - $start) . " s \n\n";
    // }

    /**
     * Test for uploading files
     */
    // public function create() : ResponseInterface
    // {
    //     $Response = parent::get_structured_ok_response();
    //     $struct =& $Response->getBody()->getStructure();
    //     $struct['message'] = sprintf(t::_('This is the Create page'));

    //     $Request = parent::get_request();
    //     $uploadedFiles = uploadedFile::parseUploadedFiles($Request->getUploadedFiles());

    //     $struct['uploaded_files_messages'] = [];

    //     if (!empty($uploadedFiles))  {

    //         foreach ($uploadedFiles as $uploadedFile) {

    //             if (is_array($uploadedFile)) {
    //                 foreach ($uploadedFile as $subUploadedFile) {
    //                     $uploaded_file_result = $this->uploadFile($subUploadedFile, $message);
    //                     $struct['uploaded_files_messages'][] = $message;
    //                 }
    //             } else {
    //                 $uploaded_file_result = $this->uploadFile($uploadedFile, $message);
    //                 $struct['uploaded_files_messages'][] = $message;
    //             }
    //         }
    //     }

    //     return $Response;
    // }

    // private function uploadFile(\Guzaba2\Http\UploadedFile $fileToUpload, &$message) : String
    // {
    //     $directory = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'uploads';

    //     $extension = pathinfo($fileToUpload->getClientFilename(), PATHINFO_EXTENSION);
    //     $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    //     $filename = sprintf('%s.%0.8s', $basename, $extension);
    //     try {
    //         $fileToUpload->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    //         $message = '<h3>File ' . $fileToUpload->getClientFilename() . ' is uploaded in ' . $directory . DIRECTORY_SEPARATOR . $filename . '!</h3>';
    //         return true;
    //     } catch (\Exception $e) {
    //         $message = $e->getMessage();
    //         //$message = $directory . DIRECTORY_SEPARATOR . $filename;
    //         return false;
    //     }
    // }
}