<?php
/**
 * @see https://github.com/dotkernel/dot-form/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-form/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

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
