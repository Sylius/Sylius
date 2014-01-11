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

class OrderCurrencyListenerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\MoneyBundle\Context\CurrencyContextInterface $currencyContext
     */
    function let($currencyContext)
    {
        $this->beConstructedWith($currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderCurrencyListener');
    }

    /**
     * @param Sylius\Bundle\CartBundle\Event\CartEvent       $event
     * @param \stdClass                                      $invalidSubject
     */
    function it_throws_exception_if_event_has_non_order_subject($event, $invalidSubject)
    {
        $event->getCart()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringProcessOrderCurrency($event)
        ;
    }

    /**
     * @param Sylius\Bundle\CartBundle\Event\CartEvent       $event
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface  $order
     */
    function it_sets_currency_on_order($currencyContext, $event, $order)
    {
        $event->getCart()->willReturn($order);
        $currencyContext->getCurrency()->shouldBeCalled()->willReturn('PLN');

        $this->processOrderCurrency($event);
    }
}
