<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/22/16
 * Time: 9:05 PM
 */

namespace nicolascajelli\server;


use nicolascajelli\server\datatype\HttpError;
use nicolascajelli\server\di\Service;
use nicolascajelli\server\exception\BadRequestException;
use nicolascajelli\server\exception\NotFoundException;
use nicolascajelli\server\exception\RestException;
use nicolascajelli\server\response\ErrorResponse;
use nicolascajelli\server\response\Response;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class RestHandler
{
    /**
     * @var Request
     */
    protected $_request;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Fussball constructor.
     */
    public function __construct()
    {
        $this->buildContainer();
        $this->_request = $this->container->get('nicolascajelli.server.request.Request');
        set_exception_handler([$this, 'dispatchError']);
    }

    public function dispatchError(\Throwable $e)
    {
        if ($e instanceof RestException) {
            $errorCode = $e->getHttpCode();
        } else {
            $errorCode = HttpError::ERROR_500;
        }
        $response = new ErrorResponse($e->getMessage(), $errorCode);
        foreach ($response->getHeaders() as $header) {
            header($header);
        }
        $this->renderResponse($response);
    }

    public function dispatch()
    {
        $map = $this->_getCallMapping();

        if (! isset($map[$this->_request->getMethod()])) {
            throw new BadRequestException("Method is not defined for this resource.");
        }
        $controllerClass = $map['controller'];
        $controller = $this->container->get(str_replace('\\', '.', trim($controllerClass, '\\')));
        $args = [];

        foreach ($map[$this->_request->getMethod()]['args'] as $arg) {
            if ($arg instanceof ScalarArgument) {
                $args[] = $arg->getValue();
            } else {
                $args[] = new $arg($this->_request);
            }
        }
        $response = call_user_func_array(
            [
                $controller,
                $map[$this->_request->getMethod()]['method']
            ],
            $args
        );
        $this->renderResponse($response);
    }

    protected function _getCallMapping()
    {
        $mapping = require_once BASE_DIR . '/build/path_mapping.php';
        if (isset($mapping['simple_paths'][$this->_request->getUri()])) {
            $map = $mapping['simple_paths'][$this->_request->getUri()];
            return $map;
        } else {
            foreach ($mapping['dynamic_paths'] as $candidate => $config) {
                $known = substr($candidate, 0, strpos($candidate, '{'));
                if (substr($this->_request->getUri(), 0, strlen($known)) == $known) {
                    if (preg_match('#' . $candidate . '#', $this->_request->getUri(), $matches)) {
                        array_shift($matches);
                        $args = [];
                        foreach ($matches as $arg) {
                            $args[] = new ScalarArgument($arg);
                        }
                        $config[$this->_request->getMethod()]['args'] = $args;
                        return $config;
                    }

                }
            }
        }
        throw new NotFoundException('Unknown resource.');
    }

    protected function renderResponse(Response $response)
    {
        $response->render();
    }

    protected function buildContainer()
    {
        $this->container = new ContainerBuilder();
        $services = $this->getServices();
        foreach ($services as $service) {
            $service->register($this->container);
        }
    }

    /**
     * @return Service[]
     */
    protected function getServices()
    {
        return require_once BASE_DIR . "/build/services_mapping.php";
    }

}