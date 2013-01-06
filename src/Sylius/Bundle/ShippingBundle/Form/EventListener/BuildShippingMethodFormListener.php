<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\EventListener;

use Sylius\Bundle\ShippingBundle\Calculator\DelegatingShippingChargeCalculator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This listener adds configuration form to a method, if
 * selected calculator requires one.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BuildShippingMethodFormListener implements EventSubscriberInterface
{
    /**
     * It hold registry of all calculators.
     *
     * @var DelegatingShippingChargeCalculator
     */
    private $delegatingCalculator;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param DelegatingShippingChargeCalculator $delegatingCalculator
     * @param FormFactoryInterface               $factory
     */
    public function __construct(DelegatingShippingChargeCalculator $delegatingCalculator, FormFactoryInterface $factory)
    {
        $this->delegatingCalculator = $delegatingCalculator;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND     => 'preBind'
        );
    }

    /**
     * Add the calculator configuration if any.
     *
     * @param DataEvent $event
     */
    public function preSetData(DataEvent $event)
    {
        $method = $event->getData();
        $form = $event->getForm();

        if (null === $method || null === $method->getId()) {
            return;
        }

        $calculator = $this->delegatingCalculator->getCalculator($method->getCalculator());

        if (!$calculator->isConfigurable()) {
            return;
        }

        $configuration = $method->getConfiguration();

        $builder = $this->factory->createNamedBuilder('configuration', 'form', $configuration, array(
            'data_class' => null
        ));

        $calculator->buildConfigurationForm($builder);

        $form->add($builder->getForm());
    }

    /**
     * Add the calculator configuration if any.
     *
     * @param DataEvent $event
     */
    public function preBind(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (empty($data) || !array_key_exists('calculator', $data)) {
            return;
        }

        $calculator = $this->delegatingCalculator->getCalculator($data['calculator']);

        if (false === $calculator->isConfigurable()) {
            return;
        }

        $builder = $this->factory->createNamedBuilder('configuration', 'form', null, array(
            'data_class' => null
        ));

        $calculator->buildConfigurationForm($builder);

        $form->add($builder->getForm());
    }
}
