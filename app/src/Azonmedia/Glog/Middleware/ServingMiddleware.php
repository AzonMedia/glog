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

    public function __construct(Server $HttpServer, array $options = [])
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



        return $Response;
    }
}