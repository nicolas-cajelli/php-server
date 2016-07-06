<?php
namespace nicolascajelli\server\di;
use Phake;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/1/16
 * Time: 10:53 PM
 */
class ServiceTest extends PHPUnit_Framework_TestCase
{

    public function testStaticCreation()
    {
        $service = Service::create('id', 'class');
        $this->assertInstanceOf(Service::class, $service);
        $this->assertEquals('id', $service->getId());
        $this->assertEquals('class', $service->getClass());
    }

    public function testRegisterService()
    {
        $service = Service::create('id', 'class');
        $container = Phake::mock(ContainerBuilder::class);
        $definition = Phake::mock(Definition::class);
        Phake::when($container)->register('id', 'class')->thenReturn($definition);

        $service->register($container);

        Phake::verify($container)->register('id', 'class');
        Phake::verify($definition, Phake::never())->setShared(Phake::anyParameters());
        Phake::verify($definition, Phake::never())->addArgument(Phake::anyParameters());
    }

    public function testRegisterNonSharedService()
    {
        $service = Service::create('id', 'class');
        $container = Phake::mock(ContainerBuilder::class);
        $definition = Phake::mock(Definition::class);
        Phake::when($container)->register('id', 'class')->thenReturn($definition);
        $service->setShared(false);
        $service->register($container);
        Phake::verify($container)->register('id', 'class');
        Phake::verify($definition)->setShared(false);
        Phake::verify($definition, Phake::never())->addArgument(Phake::anyParameters());
    }

    public function testRegisterServiceWithArguments()
    {
        $service = Service::create('id', 'class');
        $service->withReferences(['ref']);
        $container = Phake::mock(ContainerBuilder::class);
        $definition = Phake::mock(Definition::class);
        Phake::when($container)->register('id', 'class')->thenReturn($definition);
        $service->register($container);
        Phake::verify($container)->register('id', 'class');
        Phake::verify($definition, Phake::never())->setShared(Phake::anyParameters());
        Phake::verify($definition)->addArgument(Phake::anyParameters());
    }
}