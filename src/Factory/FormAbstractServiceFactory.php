<?php
/**
 * @copyright: DotKernel
 * @library: dot-form
 * @author: n3vrax
 * Date: 1/24/2017
 * Time: 1:41 AM
 */

namespace Dot\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\InputFilter\Factory;
use Zend\Stdlib\ArrayUtils;

/**
 * Class FormAbstractServiceFactory
 * @package Dot\Form\Factory
 */
class FormAbstractServiceFactory extends \Zend\Form\FormAbstractServiceFactory
{
    const PREFIX = 'dot-form';

    /** @var string  */
    protected $configKey = 'dot_form';

    /** @var string  */
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
     * @return \Zend\Form\ElementInterface
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
                && is_array($config[$extendsConfigKey])) {
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
    protected function getConfig(ContainerInterface $container)
    {
        parent::getConfig($container);
        if (!empty($this->config)) {
            if (isset($this->config[$this->subConfigKey]) && is_array($this->config[$this->subConfigKey])) {
                $this->config = $this->config[$this->subConfigKey];
            }
        }

        return $this->config;
    }

    protected function getFormFactory(ContainerInterface $container)
    {
        $formFactory = parent::getFormFactory($container);
        if ($container->has('InputFilterManager')) {
            $formFactory->setInputFilterFactory(new Factory($container->get('InputFilterManager')));
        }

        return $formFactory;
    }
}
