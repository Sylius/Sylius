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

use Sylius\Bundle\PricingBundle\Model\PriceableInterface;
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
class BuildPriceableFormListener implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    public function __construct(ServiceRegistryInterface $calculatorRegistry, FormFactoryInterface $factory)
    {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $priceable = $event->getData();

        if (null === $priceable) {
            return;
        }

        if (!$priceable instanceof PriceableInterface) {
            throw new UnexpectedTypeException($priceable, 'Sylius\Component\Pricing\Model\PriceableInterface');
        }

        $this->addConfigurationFields($event->getForm(), $priceable->getPricingCalculator(), $priceable->getPricingConfiguration());
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();


        if (empty($data) || !array_key_exists('pricingCalculator', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['pricingCalculator']);
    }

    /**
     * Add configuration fields to the form.
     *
     * @param FormInterface $form
     * @param string        $calculatorType
     * @param array         $data
     */
    protected function addConfigurationFields(FormInterface $form, $calculatorType, array $data = array())
    {
        $calculator = $this->calculatorRegistry->get($calculatorType);
        $formType = $calculator->getConfigurationFormType();

        if (!$formType) {
            return;
        }

        $configurationField = $this->factory->createNamed('pricingConfiguration', $formType, $data, array('auto_initialize' => false));

        $form->add($configurationField);
    }
}
