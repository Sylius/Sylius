<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\EventListener;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeValueFormListener implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var ServiceRegistryInterface
     */
    protected $attributeTypeRegistry;

    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @param FormFactoryInterface     $factory
     * @param ServiceRegistryInterface $attributeTypeRegistry
     * @param string                   $subjectName
     */
    public function __construct(FormFactoryInterface $factory, ServiceRegistryInterface $attributeTypeRegistry, $subjectName)
    {
        $this->factory = $factory;
        $this->attributeTypeRegistry = $attributeTypeRegistry;
        $this->subjectName = $subjectName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'buildForm',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function buildForm(FormEvent $event)
    {
        $attributeValue = $event->getData();
        $form = $event->getForm();
        $options = array('label' => false, 'auto_initialize' => false);

        if (null === $attributeValue) {
            $form->add($this->factory->createNamed('value', 'sylius_attribute_type_text', null, $options));

            return;
        }

        $attribute = $attributeValue->getAttribute();
        $attributeType = $this->attributeTypeRegistry->get($attribute->getType());
        $options['label'] = $attribute->getName();

        $form
            ->add($this->factory->createNamed('value', 'sylius_attribute_type_'.$attributeType->getType(), $attributeValue->getValue(), $options))
        ;
    }
}
