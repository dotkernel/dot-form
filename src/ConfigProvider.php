<?php
/**
 * @copyright: DotKernel
 * @library: dot-form
 * @author: n3vrax
 * Date: 1/24/2017
 * Time: 1:16 AM
 */

namespace Dot\Form;

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
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),
            'view_helpers' => $this->getViewHelpersConfig(),

            'dot_form' => [

                'form_manager' => [],

                'forms' => [],

            ],
        ];
    }

    public function getDependenciesConfig()
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

    public function getViewHelpersConfig()
    {
        $zendFormConfigProvider = new \Zend\Form\ConfigProvider();
        return $zendFormConfigProvider->getViewHelperConfig();
    }
}
