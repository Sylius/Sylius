<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Provider;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use PHPStan\Type\IterableType;
use Sylius\Bundle\ApiBundle\Payment\ApiPaymentMethodInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class PaymentConfigurationProviderSpec extends ObjectBehavior
{
    function let(Collection $apiPayments): void
    {
        $this->beConstructedWith($apiPayments);
    }

    function it_provides_payment_data_if_payment_is_supported(
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        ApiPaymentMethodInterface $apiPaymentMethod,
        Collection $apiPayments
    ): void {
        $apiPayments->toArray()->willReturn([$apiPaymentMethod]);

        $payment->getMethod()->willReturn($paymentMethod);

        $apiPaymentMethod->supports($paymentMethod)->willReturn(true);

        $apiPaymentMethod->provideConfiguration($payment)->willReturn(['test']);

        $this->provide($payment)->shouldReturn(['test']);
    }
}
