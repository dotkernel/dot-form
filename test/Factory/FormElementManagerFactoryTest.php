<?php

declare(strict_types=1);

namespace DotTest\Form\Factory;

use Dot\Form\Factory\FormElementManagerFactory;
use Dot\Form\FormElementManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FormElementManagerFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('get')->willReturn([
            'dot_form' => [
                'form_manager' => [],
            ],
        ]);

        $formElementManagerFactory = (new FormElementManagerFactory())($container);

        $this->assertInstanceOf(FormElementManager::class, $formElementManagerFactory);
    }
}
