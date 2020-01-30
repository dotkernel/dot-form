<?php
/**
 * @see https://github.com/dotkernel/dot-form/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-form/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Form;

use Dot\Form\Element\EntitySelect;
use Dot\Form\Factory\EntitySelectFactory;
use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\Factory\FormElementManagerFactory;
use Laminas\Form\Annotation\AnnotationBuilder;
use Laminas\Form\Annotation\AnnotationBuilderFactory;

/**
 * Class ConfigProvider
 * @package Dot\Form
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),
            'view_helpers' => $this->getViewHelpersConfig(),

            'dot_form' => [

                'form_manager' => [
                    'factories' => [
                        EntitySelect::class => EntitySelectFactory::class,
                    ],
                    'aliases' => [
                        'EntitySelect' => EntitySelect::class,
                        'entityselect' => EntitySelect::class,
                        'entitySelect' => EntitySelect::class,
                    ]
                ],

                'forms' => [],

            ],
        ];
    }

    public function getDependenciesConfig(): array
    {
        return [
            'abstract_factories' => [
                FormAbstractServiceFactory::class,
            ],
            'aliases' => [
                'Laminas\Form\Annotation\FormAnnotationBuilder' => 'FormAnnotationBuilder',
                AnnotationBuilder::class => 'FormAnnotationBuilder',
                FormElementManager::class => 'FormElementManager',
            ],
            'factories' => [
                'FormElementManager' => FormElementManagerFactory::class,
                'FormAnnotationBuilder' => AnnotationBuilderFactory::class,
            ]
        ];
    }

    public function getViewHelpersConfig(): array
    {
        $laminasFormConfigProvider = new \Laminas\Form\ConfigProvider();
        return $laminasFormConfigProvider->getViewHelperConfig();
    }
}
