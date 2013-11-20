<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\EventListener;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * A form resize listener capable of coping with a zone member collection.
 *
 * @author Tim Nagel <t.nagel@infinite.net.au>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ResizeZoneMemberCollectionListener extends ResizeFormListener
{
    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * Stores an array of Types with the Type name as the key.
     *
     * @var FormTypeInterface[]
     */
    protected $typeMap = array();

    /**
     * Stores an array of types with the Data Class as the key.
     *
     * @var array
     */
    protected $classMap = array();

    public function __construct(FormFactoryInterface $factory, array $prototypes, array $options = array(), $allowAdd = false, $allowDelete = false)
    {
        $this->factory = $factory;

        foreach ($prototypes as $prototype) {
            $dataClass = $prototype->getConfig()->getDataClass();
            $type      = $prototype->getConfig()->getType();

            $typeKey = $type instanceof ResolvedFormTypeInterface ? $type->getName() : $type;
            $this->typeMap[$typeKey] = $type;
            $this->classMap[$dataClass] = $type;
        }

        $defaultType = reset($prototypes)->getConfig()->getType()->getName();

        parent::__construct($defaultType, $options, $allowAdd, $allowDelete);
    }

    /**
     * Returns the form type for the supplied object. If a specific
     * form type is not found, it will return the default form type.
     *
     * @param object $object
     *
     * @return string
     */
    protected function getTypeForObject($object)
    {
        $class = ClassUtils::getRealClass(get_class($object));

        if (array_key_exists($class, $this->classMap)) {
            return $this->classMap[$class];
        }

        return $this->type;
    }

    /**
     * Checks the form data for a hidden _type field that indicates
     * the form type to use to process the data.
     *
     * @param array $data
     *
     * @return string|FormTypeInterface
     * @throws \InvalidArgumentException when _type is not present or is invalid
     */
    protected function getTypeForData(array $data)
    {
        if (!array_key_exists('_type', $data) || !array_key_exists($data['_type'], $this->typeMap)) {
            throw new \InvalidArgumentException('Unable to determine the Type for given data');
        }

        return $this->typeMap[$data['_type']];
    }

    /**
     * @param FormEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) {
            $data = array();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        $form = $event->getForm();
        // First remove all rows
        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        // Then add all rows again in the correct order
        foreach ($data as $name => $value) {
            $this->createFormField($form, $this->getTypeForObject($value), $name);
        }
    }

    /**
     * @param FormEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data || '' === $data) {
            $data = array();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        $form = $event->getForm();
        // Remove all empty rows
        if ($this->allowDelete) {
            foreach ($form as $name => $child) {
                if (!isset($data[$name])) {
                    $form->remove($name);
                }
            }
        }

        // Add all additional rows
        if ($this->allowAdd) {
            foreach ($data as $name => $value) {
                if (!$form->has($name)) {
                    $this->createFormField($form, $this->getTypeForData($value), $name);
                }
            }
        }
    }

    /**
     * @param FormInterface            $form
     * @param string|FormTypeInterface $type
     * @param string                   $name
     */
    private function createFormField(FormInterface $form, $type, $name)
    {
        $form->add($this->factory->createNamed($name, $type, null, array_replace(array(
            'property_path'   => '['.$name.']',
            'auto_initialize' => false
        ), $this->options)));
    }
}
