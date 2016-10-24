<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Payment\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Factory\PaymentFactory;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PaymentFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $paymentFactory)
    {
        $this->beConstructedWith($paymentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentFactory::class);
    }

    function it_implements_Sylius_shipment_factory_interface()
    {
        $this->shouldImplement(PaymentFactoryInterface::class);
    }

    function it_implements_factory_interface()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_delegates_creation_of_new_resource(FactoryInterface $paymentFactory, PaymentInterface $payment)
    {
        $paymentFactory->createNew()->willReturn($payment);

        $this->createNew()->shouldReturn($payment);
    }

    function it_creates_payment_with_currency_and_amount(
        FactoryInterface $paymentFactory,
        PaymentInterface $payment
    ) {
        $paymentFactory->createNew()->willReturn($payment);

        $payment->setAmount(1234)->shouldBeCalled();
        $payment->setCurrencyCode('EUR')->shouldBeCalled();

        $this->createWithAmountAndCurrencyCode(1234, 'EUR');
    }
}
