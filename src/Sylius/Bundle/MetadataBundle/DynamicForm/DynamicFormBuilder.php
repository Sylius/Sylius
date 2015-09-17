<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\DynamicForm;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class DynamicFormBuilder implements DynamicFormBuilderInterface
{
    /**
     * @var DynamicFormsChoicesMapInterface
     */
    protected $dynamicFormsChildrenMap;

    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * @param DynamicFormsChoicesMapInterface $dynamicFormsChildrenMap
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(DynamicFormsChoicesMapInterface $dynamicFormsChildrenMap, PropertyAccessorInterface $propertyAccessor)
    {
        $this->dynamicFormsChildrenMap = $dynamicFormsChildrenMap;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDynamicForm(FormBuilderInterface $builder, $name, $type, array $options = [])
    {
        $builder->add($name, $type, $options);

        $this->addModelDataListener($builder, $name, $builder->get($name)->getOption('group'));
        $this->addSubmittedDataListener($builder, $name);
    }

    /**
     * @param FormBuilderInterface $dynamicFormBuilder
     * @param FormInterface $dynamicForm
     * @param string $formName
     */
    private function addEmbeddedFormField(FormBuilderInterface $dynamicFormBuilder, FormInterface $dynamicForm, $formName)
    {
        $embeddedForm = $dynamicFormBuilder->getFormFactory()->createNamed(
            $dynamicFormBuilder->getName(),
            $formName,
            null,
            ['auto_initialize' => false]
        );

        $dynamicForm->add($embeddedForm);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $name
     * @param string $group
     */
    private function addModelDataListener(FormBuilderInterface $builder, $name, $group)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder, $name, $group) {
            try {
                $data = $this->propertyAccessor->getValue($event->getData(), $name);
            } catch (\RuntimeException $exception) {
                $data = null;
            }

            if (null === $data || !is_object($data)) {
                return;
            }

            $formName = $this->dynamicFormsChildrenMap->getFormNameByGroupAndDataClass($group, get_class($data));

            $options = $event->getForm()->get($name)->get('_form')->getConfig()->getOptions();
            $event->getForm()->get($name)->add('_form', 'choice', array_merge($options, ['data' => $formName]));

            $this->addEmbeddedFormField($builder->get($name), $event->getForm()->get($name), $formName);
        });
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $name
     */
    private function addSubmittedDataListener(FormBuilderInterface $builder, $name)
    {
        $builder->get($name)->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($builder, $name) {
            $formName = $event->getData()['_form'];
            if (empty($formName)) {
                $event->setData([$name => null]);
            }

            $this->addEmbeddedFormField($builder->get($name), $event->getForm(), $formName ?: 'text');
        });
    }
}
