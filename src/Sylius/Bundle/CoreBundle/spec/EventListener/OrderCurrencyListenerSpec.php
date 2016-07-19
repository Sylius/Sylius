<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\OrderCurrencyListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin OrderCurrencyListener
 */
final class OrderCurrencyListenerSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith($currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderCurrencyListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event)
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('processOrderCurrency', [$event])
        ;
    }

    function it_sets_currency_code_on_order(
        CurrencyContextInterface $currencyContext,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);

        $currencyContext->getCurrencyCode()->willReturn('EUR');

        $order->setCurrencyCode('EUR')->shouldBeCalled();

        $this->processOrderCurrency($event);
    }
}
