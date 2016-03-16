<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\Form\EventListener;

use Sylius\Component\Pricing\Calculator\Calculators;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds configuration form to the priceable object.
 * if selected calculator requires one.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildPriceableFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param FormFactoryInterface $factory
     */
    public function __construct(ServiceRegistryInterface $calculatorRegistry, FormFactoryInterface $factory)
    {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $priceable = $event->getData();

        if (null === $priceable) {
            return;
        }

        if (!$priceable instanceof PriceableInterface) {
            throw new UnexpectedTypeException($priceable, PriceableInterface::class);
        }

        $this->addConfigurationFields($event->getForm(), $priceable->getPricingCalculator(), $priceable->getPricingConfiguration());
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('pricingCalculator', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['pricingCalculator']);
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $priceable = $event->getData();

        if (!$priceable instanceof PriceableInterface) {
            throw new UnexpectedTypeException($priceable, PriceableInterface::class);
        }

        if (null === $priceable->getPricingCalculator()) {
            $priceable->setPricingCalculator(Calculators::STANDARD);
        }
    }

    /**
     * Add configuration fields to the form.
     *
     * @param FormInterface $form
     * @param string        $calculatorType
     * @param array         $data
     */
    protected function addConfigurationFields(FormInterface $form, $calculatorType, array $data = [])
    {
        $calculator = $this->calculatorRegistry->get($calculatorType);
        $formType = sprintf('sylius_price_calculator_%s', $calculator->getType());

        try {
            $configurationField = $this->factory->createNamed('pricingConfiguration', $formType, $data, ['auto_initialize' => false]);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $form->add($configurationField);
    }
}
