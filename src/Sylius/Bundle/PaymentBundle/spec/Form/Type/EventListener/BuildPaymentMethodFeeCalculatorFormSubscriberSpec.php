<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentBundle\Form\Type\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Calculator\FeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildPaymentMethodFeeCalculatorFormSubscriberSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $feeCalculatorRegistry, FormFactoryInterface $factory)
    {
        $this->beConstructedWith($feeCalculatorRegistry, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\Form\Type\EventListener\BuildPaymentMethodFeeCalculatorFormSubscriber');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_pre_sets_data(
        $factory,
        $feeCalculatorRegistry,
        FeeCalculatorInterface $feeCalculator,
        FormEvent $event,
        FormInterface $configurationForm,
        FormInterface $form,
        PaymentMethodInterface $paymentMethod
    ) {
        $event->getData()->willReturn($paymentMethod)->shouldBeCalled();
        $event->getForm()->willReturn($form)->shouldBeCalled();

        $paymentMethod->getFeeCalculator()->willReturn('test');
        $paymentMethod->getFeeCalculatorConfiguration()->willReturn(array('amount' => 100));

        $feeCalculatorRegistry->get('test')->willReturn($feeCalculator)->shouldBeCalled();
        $feeCalculator->getType()->willReturn('test');

        $factory
            ->createNamed(
                'feeCalculatorConfiguration',
                'sylius_fee_calculator_test',
                array('amount' => 100),
                array('auto_initialize' => false)
            )
            ->willReturn($configurationForm)
            ->shouldBeCalled()
        ;

        $form->add($configurationForm)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_does_not_process_if_event_has_no_data_while_pre_set(FormEvent $event)
    {
        $event->getData()->willReturn(null)->shouldBeCalled();

        $this->preSetData($event)->shouldReturn(null);
    }

    function it_throws_exception_if_event_data_is_not_payment_method_object_while_pre_set(FormEvent $event)
    {
        $event->getData()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Payment\Model\PaymentMethodInterface'))->during('preSetData', array($event));
    }

    function it_is_triggered_pre_bind(
        $factory,
        $feeCalculatorRegistry,
        FeeCalculatorInterface $feeCalculator,
        FormEvent $event,
        FormInterface $configurationForm,
        FormInterface $form,
        PaymentMethodInterface $paymentMethod
    ) {
        $event->getData()->willReturn(array('feeCalculator' => 'test'))->shouldBeCalled();
        $event->getForm()->willReturn($form)->shouldBeCalled();

        $paymentMethod->getFeeCalculator()->willReturn('test');
        $paymentMethod->getFeeCalculatorConfiguration()->willReturn(array());

        $feeCalculatorRegistry->get('test')->willReturn($feeCalculator)->shouldBeCalled();
        $feeCalculator->getType()->willReturn('test');

        $factory
            ->createNamed(
                'feeCalculatorConfiguration',
                'sylius_fee_calculator_test',
                array(),
                array('auto_initialize' => false)
            )
            ->willReturn($configurationForm)
            ->shouldBeCalled()
        ;

        $form->add($configurationForm)->shouldBeCalled();

        $this->preBind($event);
    }

    function it_does_not_process_if_event_data_is_empty_or_has_no_fee_calculator_field_while_pre_bind(FormEvent $event)
    {
        $event->getData()->willReturn(array())->shouldBeCalled();

        $this->preBind($event)->shouldReturn(null);

        $event->getData()->willReturn(array('badKeys' => ''))->shouldBeCalled();

        $this->preBind($event)->shouldReturn(null);
    }
}