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
use Symfony\Component\Form\FormEvent;
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
     * @param CalculatorRegistryInterface $calculatorRegistry
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
            FormEvents::PRE_SUBMIT   => 'preBind'
        );
    }

    /**
     * Add the calculator configuration if any.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $method = $event->getData();

        if (null === $method || null === $method->getId()) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $method->getCalculator(), $method->getConfiguration());
    }

    /**
     * Add the calculator configuration if any.
     *
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('calculator', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['calculator']);
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

        if (!$calculator->isConfigurable()) {
            return;
        }

        $configurationField = $this->factory->createNamed('configuration', $calculator->getConfigurationFormType(), $data, array('auto_initialize' => false));

        $form->add($configurationField);
    }
}
