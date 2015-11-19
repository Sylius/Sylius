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
use Sylius\Bundle\CoreBundle\EventListener\OrderTaxationListener;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface;
use Sylius\Component\Core\OrderProcessing\TaxationRemoverInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderTaxationListenerSpec extends ObjectBehavior
{
    function let(
        TaxationProcessorInterface $taxationProcessor,
        TaxationRemoverInterface $taxationRemover,
        CartProviderInterface $cartProvider
    )
    {
        $this->beConstructedWith($taxationProcessor, $taxationRemover, $cartProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderTaxationListener::class);
    }

    function it_throws_exception_if_event_has_non_order_subject(
        Event $event,
        CartProviderInterface $cartProvider,
        \stdClass $invalidSubject
    )
    {
        $cartProvider->getCart()->willReturn($invalidSubject);

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringApplyTaxes($event)
        ;

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringRemoveTaxes($event)
        ;
    }


    function it_delegates_apply_taxes_to_taxation_processor(
        TaxationProcessorInterface $taxationProcessor,
        Event $event,
        OrderInterface $order,
        CartProviderInterface $cartProvider
    ) {
        $cartProvider->getCart()->willReturn($order);
        $taxationProcessor->applyTaxes($order)->shouldBeCalled();
        $order->calculateTotal()->shouldBeCalled();

        $this->applyTaxes($event);
    }

    function it_delegates_remove_taxes_to_taxation_remover(
        TaxationRemoverInterface $taxationRemover,
        Event $event,
        OrderInterface $order,
        CartProviderInterface $cartProvider
    ) {
        $cartProvider->getCart()->willReturn($order);

        $taxationRemover->removeTaxes($order)->shouldBeCalled();
        $order->calculateTotal()->shouldBeCalled();

        $this->removeTaxes($event);
    }
}
