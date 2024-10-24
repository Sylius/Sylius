<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class CompositePaymentConfigurationProviderSpec extends ObjectBehavior
{
    function let(PaymentConfigurationProviderInterface $apiPaymentMethod): void
    {
        $this->beConstructedWith([$apiPaymentMethod]);
    }

    function it_provides_payment_data_if_payment_is_supported(
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        PaymentConfigurationProviderInterface $apiPaymentMethod,
    ): void {
        $payment->getMethod()->willReturn($paymentMethod);

        $apiPaymentMethod->supports($paymentMethod)->willReturn(true);

        $apiPaymentMethod->provideConfiguration($payment)->willReturn(['payment_data' => 'PAYMENT_DATA']);

        $this->provide($payment)->shouldReturn(['payment_data' => 'PAYMENT_DATA']);
    }

    function it_returns_empty_array_if_payment_is_not_supported(
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        PaymentConfigurationProviderInterface $apiPaymentMethod,
    ): void {
        $payment->getMethod()->willReturn($paymentMethod);

        $apiPaymentMethod->supports($paymentMethod)->willReturn(false);

        $apiPaymentMethod->provideConfiguration($payment)->shouldNotBeCalled();

        $this->provide($payment)->shouldReturn([]);
    }

    function it_supports_more_than_one_payment_method(
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        PaymentConfigurationProviderInterface $apiPaymentMethodOne,
        PaymentConfigurationProviderInterface $apiPaymentMethodTwo,
    ): void {
        $this->beConstructedWith([$apiPaymentMethodOne, $apiPaymentMethodTwo]);

        $payment->getMethod()->willReturn($paymentMethod);

        $apiPaymentMethodOne->supports($paymentMethod)->willReturn(false);
        $apiPaymentMethodTwo->supports($paymentMethod)->willReturn(true);

        $apiPaymentMethodOne->provideConfiguration($payment)->shouldNotBeCalled();
        $apiPaymentMethodTwo->provideConfiguration($payment)->willReturn(['payment_data_two' => 'PAYMENT_DATA_TWO']);

        $this->provide($payment)->shouldReturn(['payment_data_two' => 'PAYMENT_DATA_TWO']);
    }
}
