<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->factory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'addConfigurationFields',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function addConfigurationFields(FormEvent $event)
    {
        $attribute = $event->getData();
        $form = $event->getForm();

        try {
            $requiredFields = $this->factory->createNamed(
                'configuration',
                'sylius_attribute_type_configuration_'.$attribute->getType(),
                null,
                [
                    'auto_initialize' => false,
                    'label' => 'sylius.form.attribute_type.configuration',
                ]
            );

            $form->add($requiredFields);
        } catch (InvalidArgumentException $exception) {
        }
    }
}
