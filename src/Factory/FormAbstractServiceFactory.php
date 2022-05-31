<?php
/**
 * @see https://github.com/dotkernel/dot-form/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-form/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Form\Factory;

use Interop\Container\ContainerInterface;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Log\LoggerAbstractServiceFactory;

/**
 * Class FormAbstractServiceFactory
 * @package Dot\Form\Factory
 */
class FormAbstractServiceFactory extends LoggerAbstractServiceFactory
{
    const PREFIX = 'dot-form';

    /** @var string */
    protected $configKey = 'dot_form';

    /** @var string */
    protected $subConfigKey = 'forms';

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
        return parent::canCreate($container, $parts[1]);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Laminas\Form\ElementInterface
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

        return parent::__invoke($container, $parts[1], $options);
    }

    /**
     * @param ContainerInterface $container
     * @return array
     */
    protected function getConfig(ContainerInterface $container): array
    {
        parent::getConfig($container);
        if (!empty($this->config)) {
            if (isset($this->config[$this->subConfigKey]) && is_array($this->config[$this->subConfigKey])) {
                $this->config = $this->config[$this->subConfigKey];
            }
        }

        return $this->config;
    }

    protected function getFormFactory(ContainerInterface $container): \Laminas\Form\Factory
    {
        $formFactory = parent::getFormFactory($container);
        if ($container->has('InputFilterManager')) {
            $formFactory->setInputFilterFactory(new Factory($container->get('InputFilterManager')));
        }

        return $formFactory;
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
