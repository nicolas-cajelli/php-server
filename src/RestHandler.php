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
use nicolascajelli\server\request\Request;
use nicolascajelli\server\response\ErrorResponse;
use nicolascajelli\server\response\Response;
use nicolascajelli\server\filesystem\ProjectStructure;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RestHandler
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
    public function __construct(ContainerBuilder $container, ProjectStructure $structure)
    {
        $this->structure = $structure;
        $this->container = $container;
        set_exception_handler([$this, 'dispatchError']);
        $this->buildContainer();
        $this->_request = $this->container->get(Request::class);
        $urlBuilder = $this->container->get(ApiUrlBuilder::class);
        $urlBuilder->initRoutes($structure);
    }

    public function dispatchError(\Throwable $e)
    {
        if ($e instanceof RestException) {
            $errorCode = $e->getHttpCode();
        } else {
            $errorCode = HttpError::ERROR_500;
        }
        $response = $this->container->get(ErrorResponse::class);
        $response->setData($e->getMessage(), $errorCode);

        $this->renderResponse($response);
    }

    public function dispatch()
    {
        $map = $this->_getCallMapping();
        if (! isset($map[$this->_request->getMethod()])) {
            throw new BadRequestException("Method is not defined for this resource.");
        }
        $controllerClass = $map['controller'];
        $controller = $this->container->get($controllerClass);
        $args = [];

        foreach ($map[$this->_request->getMethod()]['args'] as $arg) {
            if ($arg instanceof ScalarArgument) {
                $args[] = $arg->getValue();
            } else {
                $args[] = $this->container->get($arg);
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
        $file = $this->structure->cd('build')->file('path_mapping.php');
        $mapping = $file->requireContent();

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
        $file = $this->structure->cd('build')->file('services_mapping.php');
        return $file->requireContent();
    }

}