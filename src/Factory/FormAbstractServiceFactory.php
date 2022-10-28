<?php
/**
 * @see https://github.com/dotkernel/dot-form/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-form/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Form\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Stdlib\ArrayUtils;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Class FormAbstractServiceFactory
 * @package Dot\Form\Factory
 */
class FormAbstractServiceFactory implements AbstractFactoryInterface
{
    const PREFIX = 'dot-form';

    /** @var null|array */
    private $config;

    /** @var string */
    protected $configKey = 'dot_form';

    /** @var string */
    protected $subConfigKey = 'forms';

    /** @var null|\Laminas\Form\Factory Form factory used to create forms */
    private ?Factory $factory = null;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $parts = explode('.', $requestedName);

        if (count($parts) !== 2) {
            return false;
        }
        if ($parts[0] !== static::PREFIX) {
            return false;
        }

        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        return isset($config[$parts[1]]);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return void
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $parts = explode('.', $requestedName);

        //merge configs if extends another form
        $config = $this->getConfig($container);
        $specificConfig = $config[$parts[1]];

        do {
            $extendsConfigKey = isset($specificConfig['extends']) && is_string($specificConfig['extends'])
                ? trim($specificConfig['extends'])
                : null;

            unset($specificConfig['extends']);

            if (!is_null($extendsConfigKey)
                && array_key_exists($extendsConfigKey, $config)
                && is_array($config[$extendsConfigKey])
            ) {
                $specificConfig = ArrayUtils::merge($config[$extendsConfigKey], $specificConfig);
            }
        } while ($extendsConfigKey != null);

        $this->config[$parts[1]] = $specificConfig;
    }

    /**
     * @param ContainerInterface $container
     * @return array
     */
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
        if (! isset($config[$this->configKey])) {
            $this->config = [];

            return $this->config;
        }

        $this->config = $config[$this->configKey];

        if (!empty($this->config)) {
            if (isset($this->config[$this->subConfigKey]) && is_array($this->config[$this->subConfigKey])) {
                $this->config = $this->config[$this->subConfigKey];
            }
        }

        return $this->config;
    }

    /**
     * @param ContainerInterface $container
     * @return \Laminas\Form\Factory
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getFormFactory(ContainerInterface $container): \Laminas\Form\Factory
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
     * Marshal the input filter into the configuration
     *
     * If an input filter is specified:
     * - if the InputFilterManager is present, checks if it's there; if so,
     *   retrieves it and resets the specification to the instance.
     * - otherwise, pulls the input filter factory from the form factory, and
     *   attaches the FilterManager and ValidatorManager to it.
     *
     * @param array $config
     */
    protected function marshalInputFilter(array &$config, ContainerInterface $container, \Laminas\Form\Factory $formFactory): void
    {
        if (! isset($config['input_filter'])) {
            return;
        }

        if ($config['input_filter'] instanceof InputFilterInterface) {
            return;
        }

        if (
            is_string($config['input_filter'])
            && $container->has('InputFilterManager')
        ) {
            $inputFilters = $container->get('InputFilterManager');
            if ($inputFilters->has($config['input_filter'])) {
                $config['input_filter'] = $inputFilters->get($config['input_filter']);
                return;
            }
        }

        $inputFilterFactory = $formFactory->getInputFilterFactory();
        $inputFilterFactory->getDefaultFilterChain()->setPluginManager($container->get('FilterManager'));
        $inputFilterFactory->getDefaultValidatorChain()->setPluginManager($container->get('ValidatorManager'));
    }
}
