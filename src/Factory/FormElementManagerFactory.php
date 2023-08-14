<?php

declare(strict_types=1);

namespace Dot\Form\Factory;

use Dot\Form\FormElementManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FormElementManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): FormElementManager
    {
        return new FormElementManager($container, $container->get('config')['dot_form']['form_manager']);
    }
}
