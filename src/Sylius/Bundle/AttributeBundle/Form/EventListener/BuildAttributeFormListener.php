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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeFormListener implements EventSubscriberInterface
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
        return array(
            FormEvents::PRE_SET_DATA => 'addOptionsFields',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function addOptionsFields(FormEvent $event)
    {
        $attribute = $event->getData();
        $form = $event->getForm();

        try {
            $optionsForm = $this->factory->createNamed(
                'options',
                'sylius_attribute_type_options_'.$attribute->getType(),
                null,
                array(
                    'auto_initialize' => false,
                    'label'           => 'sylius.attribute_type.options',
                )
            );

            $form->add($optionsForm);
        } catch (InvalidArgumentException $exception) {}
    }
}
