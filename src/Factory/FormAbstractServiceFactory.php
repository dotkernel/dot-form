<?php

declare(strict_types=1);

namespace Dot\Form\Factory;

use Laminas\Filter\FilterPluginManager;
use Laminas\Form\Factory;
use Laminas\Form\Factory as FormFactory;
use Laminas\Form\FormElementManager;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Validator\ValidatorPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function count;
use function explode;
use function is_array;
use function is_string;

class FormAbstractServiceFactory implements AbstractFactoryInterface
{
    public const PREFIX = 'dot-form';

    private ?array $config = null;

    protected string $configKey = 'dot_form';

    protected string $subConfigKey = 'forms';

    private ?FormFactory $factory = null;

    /**
     * @param string $requestedName
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        $parts = explode('.', $requestedName);
        if (count($parts) !== 2) {
            return false;
        }

        if ($parts[0] !== static::PREFIX) {
            return false;
        }

        // avoid infinite loops when looking up config
        if ($requestedName === 'config') {
            return false;
        }

        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        return isset($config[$requestedName]) && is_array($config[$requestedName]) && ! empty($config[$requestedName]);
    }

    /**
     * @param string $requestedName
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): FormInterface
    {
        $config  = $this->getConfig($container);
        $config  = $config[$requestedName];
        $factory = $this->getFormFactory($container);

        $this->marshalInputFilter($config, $container, $factory);
        return $factory->createForm($config);
    }

    protected function getConfig(ContainerInterface $container): array
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (! $container->has('config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $container->get('config');
        if (! isset($config[$this->configKey]) || ! is_array($config[$this->configKey])) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];

        if (! empty($this->config)) {
            if (isset($this->config[$this->subConfigKey]) && is_array($this->config[$this->subConfigKey])) {
                $this->config = $this->config[$this->subConfigKey];
            }
        }

        return $this->config;
    }

    protected function getFormFactory(ContainerInterface $container): FormFactory
    {
        if ($this->factory instanceof Factory) {
            return $this->factory;
        }

        $elements = null;
        if ($container->has(FormElementManager::class)) {
            $elements = $container->get(FormElementManager::class);
        }

        $this->factory = new Factory($elements);
        return $this->factory;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    protected function marshalInputFilter(array &$config, ContainerInterface $container, FormFactory $formFactory): void
    {
        if (! isset($config['input_filter'])) {
            return;
        }

        if ($config['input_filter'] instanceof InputFilterInterface) {
            return;
        }

        if (is_string($config['input_filter']) && $container->has(InputFilterPluginManager::class)) {
            $inputFilters = $container->get(InputFilterPluginManager::class);
            if ($inputFilters->has($config['input_filter'])) {
                $config['input_filter'] = $inputFilters->get($config['input_filter']);
                return;
            }
        }

        $inputFilterFactory = $formFactory->getInputFilterFactory();
        $filterChain        = $inputFilterFactory->getDefaultFilterChain();
        $filterChain?->setPluginManager(
            $container->get(FilterPluginManager::class)
        );

        $validatorChain = $inputFilterFactory->getDefaultValidatorChain();
        $validatorChain?->setPluginManager(
            $container->get(ValidatorPluginManager::class)
        );
    }
}
