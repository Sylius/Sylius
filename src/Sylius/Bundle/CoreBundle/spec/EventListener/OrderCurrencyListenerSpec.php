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
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

class OrderCurrencyListenerSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith($currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderCurrencyListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(
        CartEvent $event,
        \stdClass $invalidSubject
    )
    {
        $event->getCart()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringProcessOrderCurrency($event)
        ;
    }

    function it_sets_currency_on_order($currencyContext, CartEvent $event, OrderInterface $order)
    {
        $event->getCart()->willReturn($order);
        $currencyContext->getCurrency()->shouldBeCalled()->willReturn('PLN');

        $this->processOrderCurrency($event);
    }
}
