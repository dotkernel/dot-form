<?php
/**
 * @copyright: DotKernel
 * @library: dot-form
 * @author: n3vrax
 * Date: 1/24/2017
 * Time: 1:16 AM
 */

declare(strict_types = 1);

namespace Dot\Form;

use Dot\Form\Element\EntitySelect;
use Dot\Form\Factory\EntitySelectFactory;
use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\Factory\FormElementManagerFactory;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Annotation\AnnotationBuilderFactory;

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
                'Zend\Form\Annotation\FormAnnotationBuilder' => 'FormAnnotationBuilder',
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
        $zendFormConfigProvider = new \Zend\Form\ConfigProvider();
        return $zendFormConfigProvider->getViewHelperConfig();
    }
}
