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
use Symfony\Component\Form\FormTypeInterface;
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
     * Stores an array of Types with the Type name as the key.
     *
     * @var array
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
        foreach ($prototypes as $prototype) {
            $dataClass = $prototype->getConfig()->getDataClass();
            $types     = $prototype->getConfig()->getTypes();
            $type      = end($types);

            $typeKey = $type instanceof FormTypeInterface ? $type->getName() : $type;
            $this->typeMap[$typeKey] = $type;
            $this->classMap[$dataClass] = $type;
        }

        $defaultTypes = reset($prototypes)->getConfig()->getTypes();
        $defaultType  = end($defaultTypes);
        parent::__construct($factory, $defaultType, $options, $allowAdd, $allowDelete);
    }

    /**
     * Returns the form type for the supplied object. If a specific
     * form type is not found, it will return the default form type.
     *
     * @param object $object
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
     * @return string|FormTypeInterface
     * @throws \InvalidArgumentException when _type is not present or is invalid
     */
    protected function getTypeForData(array $data)
    {
        if (!array_key_exists('_type', $data) or !array_key_exists($data['_type'], $this->typeMap)) {
            throw new \InvalidArgumentException('Unable to determine the Type for given data');
        }

        return $this->typeMap[$data['_type']];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data) {
            $data = array();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // First remove all rows
        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        // Then add all rows again in the correct order
        foreach ($data as $name => $value) {
            $type = $this->getTypeForObject($value);
            $form->add($this->factory->createNamed($name, $type, null, array_replace(array(
                'property_path' => '['.$name.']',
            ), $this->options)));
        }
    }

    public function preBind(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data || '' === $data) {
            $data = array();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

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
                    $type = $this->getTypeForData($value);
                    $form->add($this->factory->createNamed($name, $type, null, array_replace(array(
                        'property_path' => '['.$name.']',
                    ), $this->options)));
                }
            }
        }
    }
}
