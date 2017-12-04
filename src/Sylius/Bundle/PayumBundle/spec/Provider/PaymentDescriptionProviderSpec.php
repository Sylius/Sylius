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

namespace spec\Sylius\Bundle\PayumBundle\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class PaymentDescriptionProviderSpec extends ObjectBehavior
{
    function let(TranslatorInterface $translator): void
    {
        $translator->transChoice('sylius.payum_action.payment.description', 2, [
            '%items%' => 2,
            '%total%' => 100.00,
        ])->willReturn('Payment contains 2 items for a total of 100');

        $this->beConstructedWith($translator);
    }

    function it_should_generate_a_description_string(PaymentInterface $payment, OrderInterface $order): void
    {
        $order->getItems()->willReturn(new ArrayCollection([new OrderItem(), new OrderItem()]));
        $order->getTotal()->willReturn(10000);
        $payment->getOrder()->willReturn($order);

        $this->getPaymentDescription($payment)->shouldReturn('Payment contains 2 items for a total of 100');
    }
}
