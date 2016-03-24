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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\GroupableInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderRecalculationListenerSpec extends ObjectBehavior
{
    function let(OrderRecalculatorInterface $orderRecalculator)
    {
        $this->beConstructedWith($orderRecalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderRecalculationListener');
    }

    function it_uses_order_recalculator_to_recalculate_order(
        GenericEvent $event,
        OrderInterface $order,
        OrderRecalculatorInterface $orderRecalculator
    ) {
        $event->getSubject()->willReturn($order);
        $orderRecalculator->recalculate($order)->shouldBeCalled();

        $this->recalculateOrder($event);
    }

    function it_throws_exception_if_event_subject_is_not_order(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', OrderInterface::class))
            ->during('recalculateOrder', [$event])
        ;
    }
}
