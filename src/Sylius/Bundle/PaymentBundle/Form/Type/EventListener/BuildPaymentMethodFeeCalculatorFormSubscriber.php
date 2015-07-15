<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Form\Type\EventListener;

use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class BuildPaymentMethodFeeCalculatorFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $feeCalculatorRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param ServiceRegistryInterface $feeCalculatorRegistry
     * @param FormFactoryInterface     $factory
     */
    public function __construct(ServiceRegistryInterface $feeCalculatorRegistry, FormFactoryInterface $factory)
    {
        $this->feeCalculatorRegistry = $feeCalculatorRegistry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $paymentMethod = $event->getData();

        if (null === $paymentMethod) {
            return;
        }

        if (!$paymentMethod instanceof PaymentMethodInterface) {
            throw new UnexpectedTypeException($paymentMethod, 'Sylius\Component\Payment\Model\PaymentMethodInterface');
        }

        $this->addConfigurationFields($event->getForm(), $paymentMethod->getFeeCalculator(), $paymentMethod->getFeeCalculatorConfiguration());
    }

    /**
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('feeCalculator', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['feeCalculator']);
    }

    /**
     * @param FormInterface $form
     * @param string        $feeCalculatorType
     * @param array         $data
     */
    private function addConfigurationFields(FormInterface $form, $feeCalculatorType, $data = array())
    {
        $feeCalculator = $this->feeCalculatorRegistry->get($feeCalculatorType);
        $formType = sprintf('sylius_fee_calculator_%s', $feeCalculator->getType());

        try {
            $configurationField = $this->factory->createNamed(
                'feeCalculatorConfiguration',
                $formType,
                $data,
                array('auto_initialize' => false)
            );
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        $form->add($configurationField);
    }
}
