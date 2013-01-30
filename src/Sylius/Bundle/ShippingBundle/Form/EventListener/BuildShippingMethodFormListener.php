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

use Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

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
     * @var CalculatorRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param CalculatorRegistryInterface $delegatingCalculator
     * @param FormFactoryInterface        $factory
     */
    public function __construct(CalculatorRegistryInterface $calculatorRegistry, FormFactoryInterface $factory)
    {
        $this->calculatorRegistry = $calculatorRegistry;
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

        $this->addConfigurationFields($form, $method->getCalculator(), $method->getConfiguration());
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

        $this->addConfigurationFields($form, $data['calculator']);
    }

    /**
     * Add the calculator configuration fields.
     *
     * @param FormInterface $form
     * @param string        $calculatorName
     * @param array         $data
     */
    protected function addConfigurationFields(FormInterface $form, $calculatorName, array $data = array())
    {
        $calculator = $this->calculatorRegistry->getCalculator($calculatorName);

        if (true !== $calculator->isConfigurable()) {
            return;
        }

        $configurationField = $this->factory->createNamed('configuration', $calculator->getConfigurationFormType(), $data);

        $form->add($configurationField);
    }
}
