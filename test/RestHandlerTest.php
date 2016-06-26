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
        Phake::when($build)->file('services_mapping.php')->thenReturn($servicesFile);
        $services = [$this->service];
        Phake::when($servicesFile)->requireContent()->thenReturn($services);
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
}