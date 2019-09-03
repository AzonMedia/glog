<?php
declare(strict_types=1);

namespace Azonmedia\Glog\Application;

use Guzaba2\Application\Application;
use Guzaba2\Base\Base;
use Guzaba2\Http\Method;
use Guzaba2\Http\Server;
use Guzaba2\Orm\ActiveRecord;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class GlogMiddleware extends Base
    implements MiddlewareInterface
{

    protected const CONFIG_DEFAULTS = [
        'disable_locking_on_get'    => FALSE,
    ];

    protected const CONFIG_RUNTIME = [];

    /**
     * @var Server
     */
    protected $HttpServer;

    /**
     * @var Application
     */
    protected $Application;

    public function __construct(Application $Application, Server $HttpServer)
    {
        parent::__construct();
        $this->Application = $Application;
        $this->HttpServer = $HttpServer;
    }

    /**
     * Custom logic based on the requests for Glog
     *
     * @param ServerRequestInterface $Request
     * @param RequestHandlerInterface $Handler
     * @return ResponseInterface
     * @throws \Guzaba2\Base\Exceptions\RunTimeException
     */
    public function process(ServerRequestInterface $Request, RequestHandlerInterface $Handler) : ResponseInterface
    {
        if ($Request->getMethod() === 'GET' && self::CONFIG_RUNTIME['disable_locking_on_get']) {
            ActiveRecord::disable_locking();
        }
        $Response = $Handler->handle($Request);

        return $Response;
    }

}