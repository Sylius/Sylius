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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
        GenericEvent $event,
        \stdClass $invalidSubject
    ) {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringProcessOrderCurrency($event)
        ;
    }

    function it_sets_currency_on_order(
        $currencyContext,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);
        $currencyContext->getCurrency()->shouldBeCalled()->willReturn('PLN');

        $this->processOrderCurrency($event);
    }
}
