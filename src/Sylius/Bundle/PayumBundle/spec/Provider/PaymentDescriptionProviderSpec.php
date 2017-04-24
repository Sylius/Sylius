<?php

namespace spec\Sylius\Bundle\PayumBundle\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProvider;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Stefan Doorn <stefan@efectos.nl>
 */
final class PaymentDescriptionProviderSpec extends ObjectBehavior
{
    function let(TranslatorInterface $translator)
    {
        $translator->transChoice('sylius.payum_action.payment.description', 2, [
            '%items%' => 2,
            '%total%' => 100.00,
        ])->willReturn('Payment contains 2 items for a total of 100');

        $this->beConstructedWith($translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentDescriptionProvider::class);
    }

    function it_should_generate_a_description_string(PaymentInterface $payment, OrderInterface $order)
    {
        $order->getItems()->willReturn(new ArrayCollection([new OrderItem(), new OrderItem()]));
        $order->getTotal()->willReturn(10000);
        $payment->getOrder()->willReturn($order);

        $this->getPaymentDescription($payment)->shouldReturn('Payment contains 2 items for a total of 100');
    }
}
