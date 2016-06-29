<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/26/16
 * Time: 6:01 PM
 */

namespace nicolascajelli\server;

use nicolascajelli\server\datatype\HttpError;
use nicolascajelli\server\di\Service;
use nicolascajelli\server\filesystem\Directory;
use nicolascajelli\server\filesystem\File;
use nicolascajelli\server\filesystem\ProjectStructure;
use nicolascajelli\server\response\ErrorResponse;
use nicolascajelli\server\response\EntityResponse;
use \nicolascajelli\server\exception\BadRequestException;
use \nicolascajelli\server\request\Request;
use \Phake;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestHandler
     */
    protected $handler;

    public function setup() {
        $this->containerBuilder = Phake::mock(ContainerBuilder::class);
        $projectStructure = Phake::mock(ProjectStructure::class);

        $this->service = Phake::mock(Service::class);

        $build = Phake::mock(Directory::class);
        Phake::when($projectStructure)->cd('build')->thenReturn($build);
        $servicesFile = Phake::mock(File::class);
        $pathFile = Phake::mock(File::class);
        Phake::when($build)->file('services_mapping.php')->thenReturn($servicesFile);
        Phake::when($build)->file('path_mapping.php')->thenReturn($pathFile);
        $services = [$this->service];
        Phake::when($servicesFile)->requireContent()->thenReturn($services);
        $paths = [
            'simple_paths' => [
                '/res/simple' => [
                    'controller' => '\\test\\controller\\simple',
                    'get' => [
                        'method' => 'renderSimple',
                        'args' => []    
                    ]
                ]
            ],
            'dynamic_paths' => [],
        ];
        Phake::when($pathFile)->requireContent()->thenReturn($paths);
        $this->request = Phake::mock(Request::class);
        Phake::when($this->containerBuilder)->get('nicolascajelli.server.request.Request')->thenReturn($this->request);

        $this->handler = new RestHandler($this->containerBuilder, $projectStructure);
    }

    public function testServerSetup() {
        Phake::verify($this->containerBuilder)->get('nicolascajelli.server.request.Request');
        Phake::verify($this->service)->register($this->containerBuilder);

        $exceptionHandler = set_exception_handler(null);
        $this->assertEquals($exceptionHandler[0], $this->handler);
        $this->assertEquals($exceptionHandler[1], 'dispatchError');
    }

    public function testDispatchErrorForUnknownException() {
        $response = Phake::mock(ErrorResponse::class);
        Phake::when($this->containerBuilder)->get('nicolascajelli.server.response.ErrorResponse')->thenReturn($response);
        $this->handler->dispatchError(new \Exception("Test"));

        Phake::verify($response)->setData("Test", HttpError::ERROR_500);
        Phake::verify($response)->render();
    }

    /**
     *
     */
    public function testDispatchErrorForKnownException() {
        $response = Phake::mock(ErrorResponse::class);
        Phake::when($this->containerBuilder)->get('nicolascajelli.server.response.ErrorResponse')->thenReturn($response);
        $this->handler->disPatchError(new BadRequestException("Invalid request"));
        Phake::verify($response)->setData("Invalid request", HttpError::ERROR_400);
        Phake::verify($response)->render();

    }

    /**
     * @expectedException \nicolascajelli\server\exception\NotFoundException
     * @expectedExceptionMessage Unknown resource.
     *
     */
    public function testDispatchOnUndefinedResource()
    {
        $this->handler->dispatch();
    }

    /**
     * @expectedException \nicolascajelli\server\exception\BadRequestException
     * @expectedExceptionMessage Method is not defined for this resource.
     *
     */
    public function testDispatchOnSimplePathInvalidMethod()
    {
        Phake::when($this->request)->getUri()->thenReturn("/res/simple");
        Phake::when($this->request)->getMethod()->thenReturn("post");
        $this->handler->dispatch();
    }

    public function testDispatchOnSimplePath()
    {
        $controller = new class {
            public function renderSimple() {
                return Phake::mock(EntityResponse::class);
            }
        };
        Phake::when($this->containerBuilder)->get('test.controller.simple')->thenReturn($controller);
        Phake::when($this->request)->getUri()->thenReturn("/res/simple");
        Phake::when($this->request)->getMethod()->thenReturn("get");
        $this->handler->dispatch();
    }
}