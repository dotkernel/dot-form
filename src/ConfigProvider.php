<?php

declare(strict_types=1);

namespace Dot\Form;

use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\Factory\FormElementManagerFactory;
use Laminas\Form\ConfigProvider as LaminasConfigProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),
            'view_helpers' => $this->getViewHelpersConfig(),
            'dot_form'     => [
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
            'aliases'            => [
                FormElementManager::class => 'FormElementManager',
            ],
            'factories'          => [
                'FormElementManager'      => FormElementManagerFactory::class,
            ],
        ];
    }

    public function getViewHelpersConfig(): array
    {
        $laminasFormConfigProvider = new LaminasConfigProvider();
        return $laminasFormConfigProvider->getViewHelperConfig();
    }
}
