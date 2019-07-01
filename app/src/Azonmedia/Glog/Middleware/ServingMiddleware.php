<?php
declare(strict_types=1);

namespace Azonmedia\Glog\Middleware;

use Guzaba2\Base\Base;
use Guzaba2\Http\Body\Stream;
use Guzaba2\Http\Response;
use Guzaba2\Http\StatusCode;
use Guzaba2\Swoole\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ServingMiddleware extends Base
implements MiddlewareInterface
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Server
     */
    protected $HttpServer;

    public function __construct(array $options = [], Server $HttpServer)
    {
        parent::__construct();

        $this->HttpServer = $HttpServer;

        if (!isset($options['log_dir'])) {
            $options['log_dir'] = getcwd().'/data/';
        }
        $this->options = $options;
    }

    /**
     * Accepts new log entries.
     * Pushes the saving of the log entry to a worker in a non blocking manner.
     * @link https://github.com/swoole/swoole-docs/blob/master/get-started/examples/async_task.md
     * 
     * @param ServerRequestInterface $Request
     * @param RequestHandlerInterface $Handler
     * @return ResponseInterface
     * @throws \Guzaba2\Base\Exceptions\RunTimeException
     */
    public function process(ServerRequestInterface $Request, RequestHandlerInterface $Handler) : ResponseInterface
    {
        //for testing
        //file_put_contents($this->options['log_dir'].'log.txt', time().' '.$Request->getBody()->read(8196).PHP_EOL, FILE_APPEND);//add the POST content
        $entry_content = $Request->getBody()->read(8196);
        $this->HttpServer->task($entry_content);

        $Body = new Stream();
        $output = ['code' => 1, 'message' => 'Log entry accepted'];
        $json_output = json_encode($output);
        $Body->write($json_output);
        $Response = new Response(StatusCode::HTTP_OK, ['Content-Type' => 'application/json'], $Body);

        // Example for using UploadedFile
        // $uploadedFiles = $Request->getUploadedFiles();

        // if (!empty($uploadedFiles))  {

        //     foreach ($uploadedFiles as $uploadedFile) {

        //         if (is_array($uploadedFile)) {
        //             foreach ($uploadedFile as $subUploadedFile) {
        //                 $this->uploadFile($subUploadedFile, $Body);
        //             }
        //         } else {
        //             $this->uploadFile($uploadedFile, $Body);
        //         }
        //     }
        // }

        // $Body->write('<form method="post" enctype="multipart/form-data">
        //     Select multiple files: <input type="file" name="my_file1[]" multiple ><br />
        //     Select One file: <input type="file" name="my_file2"><br />
        //     <input type="submit" value="Submit">
        //     </form>');
        // $Response = new Response(StatusCode::HTTP_OK, ['Content-Type' => 'text/html'], $Body);

        return $Response;
    }

    // Example for using UploadedFile
    // private function uploadFile(\Guzaba2\Http\UploadedFile $fileToUpload, $Body){
    //     $directory = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'uploads';

    //     $extension = pathinfo($fileToUpload->getClientFilename(), PATHINFO_EXTENSION);
    //     $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    //     $filename = sprintf('%s.%0.8s', $basename, $extension);

    //     try {
    //         $fileToUpload->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    //         $Body->write('<h3>File ' . $fileToUpload->getClientFilename() . ' is uploaded in app/src/Azonmedia/Glog/uploads!</h3>');
    //     } catch (\Exception $e) {
    //         $Body->write('<h3>File ' . $fileToUpload->getClientFilename() . ' is NOT uploaded!</h3>');
    //     }
    // }
}