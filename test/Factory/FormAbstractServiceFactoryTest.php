<?php

declare(strict_types=1);

namespace DotTest\Form\Factory;

use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\Factory\FormAbstractServiceFactory as Subject;
use Laminas\Form\Factory;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

class FormAbstractServiceFactoryTest extends TestCase
{
    private ContainerInterface|MockObject $container;

    private Subject $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->subject   = new FormAbstractServiceFactory();
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testClassImplementsAbstractFactoryInterface(): void
    {
        $this->assertInstanceOf(AbstractFactoryInterface::class, $this->subject);
    }

    public function testCanCreateReturnsFalseInvalidFormName(): void
    {
        $requestedName = 'invalidName';

        $canCreate = $this->subject->canCreate($this->container, $requestedName);
        $this->assertFalse($canCreate);
    }

    public function testCanCreateReturnsFalseMissingPrefix(): void
    {
        $requestedName = 'dot.form';

        $canCreate = $this->subject->canCreate($this->container, $requestedName);
        $this->assertFalse($canCreate);
    }

    public function testCanCreateReturnsFalseConfigProvided(): void
    {
        $requestedName = 'config';

        $canCreate = $this->subject->canCreate($this->container, $requestedName);
        $this->assertFalse($canCreate);
    }

    /**
     * @throws Exception
     */
    public function testCanCreateReturnsFalseEmptyConfig(): void
    {
        $requestedName = 'dot-form.form';
        $container     = $this->createMock(ContainerInterface::class);

        $container->method('get')->willReturn([]);

        $canCreate = $this->subject->canCreate($container, $requestedName);
        $this->assertFalse($canCreate);
    }

    /**
     * @throws Exception
     */
    public function testCanCreateReturnsFalseInvalidConfig(): void
    {
        $requestedName = 'dot-form.form';
        $container     = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())->method('has')->willReturn(true);
        $container->method('get')->willReturn(['dot_form' => []]);

        $canCreate = $this->subject->canCreate($container, $requestedName);
        $this->assertFalse($canCreate);
    }

    /**
     * @throws Exception
     */
    public function testCanCreateReturnsTrue(): void
    {
        $requestedName = 'dot-form.form';
        $container     = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn(['dot_form' => ['dot-form.form' => ['form_name']]]);

        $canCreate = $this->subject->canCreate($container, $requestedName);
        $this->assertTrue($canCreate);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetExistingConfig(): void
    {
        $config          = ['form.dot_forms'];
        $reflectionClass = new ReflectionClass(FormAbstractServiceFactory::class);
        $reflectionClass->getProperty('config')->setValue($this->subject, $config);

        $result = $reflectionClass
            ->getMethod('getConfig')
            ->invoke($this->subject, $this->container);

        $this->assertSame($config, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testConfigNotFound(): void
    {
        $this->container->expects($this->once())->method('has')->willReturn(false);

        $config = $this->callMethod($this->subject, 'getConfig', $this->container);

        $this->assertSame([], $config);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetInvalidConfig(): void
    {
        $this->container->expects($this->once())->method('has')->willReturn(true);
        $this->container->expects($this->once())->method('get')->willReturn(['form']);

        $config = $this->callMethod($this->subject, 'getConfig', $this->container);

        $this->assertSame([], $config);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetValidConfig(): void
    {
        $config = ['dot_form' => ['form_name']];
        $this->container->expects($this->once())->method('has')->willReturn(true);
        $this->container->expects($this->once())->method('get')->willReturn($config);

        $result = $this->callMethod($this->subject, 'getConfig', $this->container);

        $this->assertSame($config['dot_form'], $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetConfigSubKey(): void
    {
        $config = ['dot_form' => ['forms' => ['form_name']]];
        $this->container->expects($this->once())->method('has')->willReturn(true);
        $this->container->expects($this->once())->method('get')->willReturn($config);

        $result = $this->callMethod($this->subject, 'getConfig', $this->container);

        $this->assertSame($config['dot_form']['forms'], $result);
    }

    /**
     * @throws Exception
     */
    public function testGetExistingFormFactory(): void
    {
        $formFactory     = $this->createMock(Factory::class);
        $reflectionClass = new ReflectionClass(FormAbstractServiceFactory::class);
        $reflectionClass->getProperty('factory')->setValue($this->subject, $formFactory);

        $result = $reflectionClass
            ->getMethod('getFormFactory')
            ->invoke($this->subject, $this->container);

        $this->assertInstanceOf(Factory::class, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetFormFactoryFormElementManagerNotFound(): void
    {
        $this->container->expects($this->once())->method('has')->willReturn(false);

        $result          = $this->callMethod($this->subject, 'getFormFactory', $this->container);
        $reflectionClass = new ReflectionClass($result);

        $this->assertInstanceOf(Factory::class, $result);
        $this->assertNull($reflectionClass->getProperty('formElementManager')->getValue($result));
    }

    /**
     * @throws Exception
     */
    public function testGetFormFactoryExistingFormElementManager(): void
    {
        $formElementManager = $this->createMock(FormElementManager::class);
        $this->container->expects($this->once())->method('has')->willReturn(true);
        $this->container->expects($this->once())->method('get')->willReturn($formElementManager);

        $result          = $this->callMethod($this->subject, 'getFormFactory', $this->container);
        $reflectionClass = new ReflectionClass($result);

        $this->assertInstanceOf(Factory::class, $result);
        $this->assertInstanceOf(
            FormElementManager::class,
            $reflectionClass->getProperty('formElementManager')->getValue($result)
        );
    }

    /**
     * @throws ReflectionException
     */
    private function callMethod(object $object, string $method, mixed ...$args): mixed
    {
        $reflectionClass = new ReflectionClass($object::class);

        return $reflectionClass->getMethod($method)->invoke($object, ...$args);
    }
}
