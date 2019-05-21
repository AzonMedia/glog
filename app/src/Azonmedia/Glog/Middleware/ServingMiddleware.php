<?php
declare(strict_types=1);

namespace Azonmedia\Glog\Middleware;

use Guzaba2\Base\Base;
use Guzaba2\Http\Body\Stream;
use Guzaba2\Http\Response;
use Guzaba2\Http\StatusCode;
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

    public function __construct(array $options = [])
    {
        parent::__construct();

        if (!isset($options['log_dir'])) {
            $options['log_dir'] = getcwd().'/data/';
        }
        $this->options = $options;
    }

    public function process(ServerRequestInterface $Request, RequestHandlerInterface $Handler) : ResponseInterface
    {
        //for testing
        file_put_contents($this->options['log_dir'].'log.txt', time().' '.$Request->getBody()->read(8196).PHP_EOL, FILE_APPEND);//add the POST content

        $Body = new Stream();
        $output = ['code' => 1, 'message' => 'Log entry added'];
        $json_output = json_encode($output);
        $Body->write($json_output);
        $Response = new Response(StatusCode::HTTP_OK, ['Content-Type' => 'application/json'], $Body);
        return $Response;
    }
}