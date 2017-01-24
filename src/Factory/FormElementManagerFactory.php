<?php
/**
 * @copyright: DotKernel
 * @library: dot-form
 * @author: n3vrax
 * Date: 1/24/2017
 * Time: 1:39 AM
 */

namespace Dot\Form\Factory;

use Dot\Form\FormElementManager;
use Interop\Container\ContainerInterface;

/**
 * Class FormElementManagerFactory
 * @package Dot\Form\Factory
 */
class FormElementManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new FormElementManager($container, $container->get('config')['dot_form']['form_manager']);
    }
}
