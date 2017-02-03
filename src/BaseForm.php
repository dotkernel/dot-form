<?php
/**
 * @copyright: DotKernel
 * @library: dot-form
 * @author: n3vrax
 * Date: 2/3/2017
 * Time: 8:42 PM
 */

declare(strict_types = 1);

namespace Dot\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\Form;
use Zend\Form\FormInterface;

/**
 * Class BaseForm
 * @package Dot\Form
 */
class BaseForm extends Form
{
    /** @var array  */
    protected $currentValidationGroup = [];

    /**
     * Initialize form elements
     */
    public function init()
    {
        $csrf = new Csrf($this->getName(), [
            'csrf_options' => [
                'timeout' => 3600,
                'message' => 'The form used to make the request has expired. Please try again now'
            ]
        ]);
        $this->add($csrf, ['priority' => -9999]);
    }

    /**
     * @param $inputName
     * @param bool $flag
     */
    public function setValidationForInput(string $inputName, bool $flag)
    {
        $parts = explode('.', $inputName);
        $lastKey = array_pop($parts);
        $val = &$this->currentValidationGroup;
        foreach ($parts as $part) {
            $val = &$val[$part];
        }
        $val[$lastKey] = $flag;
    }

    /**
     * @param $group
     */
    public function resetValidationGroup(array &$group)
    {
        foreach ($group as $key => $value) {
            if (is_array($value)) {
                $this->resetValidationGroup($value);
            } else {
                $group[$key] = true;
            }
        }
        $this->setValidationGroup(FormInterface::VALIDATE_ALL);
    }

    /**
     * Set current validation group to the form
     */
    public function applyValidationGroup()
    {
        $validationGroup = $this->getActiveValidationGroup($this->currentValidationGroup, $this);
        $this->setValidationGroup($validationGroup);
    }

    /**
     * @param $groups
     * @param ElementInterface $prevElement
     * @return array
     */
    public function getActiveValidationGroup(array $groups, ElementInterface $prevElement): array
    {
        $validationGroup = [];
        foreach ($groups as $key => $value) {
            if (is_array($value) &&
                ($prevElement instanceof FieldsetInterface || $prevElement instanceof FormInterface)) {
                if ($prevElement->has($key)) {
                    $validationGroup[$key] = $this->getActiveValidationGroup($value, $prevElement->get($key));
                }
            } elseif ($value === true && $prevElement->has($key)) {
                $validationGroup[] = $key;
            }
        }
        return $validationGroup;
    }
}
