<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\EventListener;

use Sylius\Bundle\ProductBundle\Model\PropertyTypes;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Form event listener that builds product property form dynamically based on product data.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class BuildProductPropertyFormListener implements EventSubscriberInterface
{
    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $factory
     */
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'buildForm');
    }

    /**
     * Builds proper product form after setting the product.
     *
     * @param FormEvent $event
     */
    public function buildForm(FormEvent $event)
    {
        $productProperty = $event->getData();
        $form = $event->getForm();
        $options = array(
            'label' => 'sylius.product.property.label.value',
            'auto_initialize' => false
        );

        if (null === $productProperty) {
            $form->add($this->factory->createNamed('value', 'text', null, $options));

            return;
        }

        $options['label'] = $productProperty->getName();

        if (is_array($productProperty->getConfiguration()) &&
            PropertyTypes::CHOICE == $productProperty->getType()) {
            $options['choices'] = $this->getChoices($productProperty->getConfiguration());
        }

        // If we're editing the product property, let's just render the value field, not full selection.
        $form
            ->remove('property')
            ->add($this->factory->createNamed('value', $productProperty->getType(), null, $options))
        ;
    }

    private function getChoices($configuration)
    {
        $choices = array();
        foreach ($configuration as $choice) {
            $choices[strtolower($choice['configuration'])] = $choice['configuration'];
        }

        return $choices;
    }
}
