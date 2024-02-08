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

namespace spec\Sylius\Bundle\PayumBundle\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PaymentDescriptionProviderSpec extends ObjectBehavior
{
    function let(TranslatorInterface $translator): void
    {
        $translator->trans('sylius.payum_action.payment.description', [
            '%items%' => 2,
            '%total%' => 100.00,
        ])->willReturn('Payment contains 2 items for a total of 100');

        $this->beConstructedWith($translator);
    }

    function it_should_generate_a_description_string(PaymentInterface $payment, OrderInterface $order): void
    {
        $order->getItems()->willReturn(new ArrayCollection([new OrderItem(), new OrderItem()]));

        $payment->getOrder()->willReturn($order);
        $payment->getAmount()->willReturn(10000);

        $this->getPaymentDescription($payment)->shouldReturn('Payment contains 2 items for a total of 100');
    }
}
