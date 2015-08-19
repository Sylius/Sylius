<?php

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\GroupableInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
class OrderPricingListenerSpec extends ObjectBehavior
{
    function let(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->beConstructedWith($priceCalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderPricingListener');
    }

    function it_should_throw_an_exception_if_its_subjet_is_not_order_interface(GenericEvent $event)
    {
        $wrongOrderClass = new \stdClass();
        $exception = new UnexpectedTypeException($wrongOrderClass, 'Sylius\Component\Order\Model\OrderInterface');

        $event->getSubject()->shouldBeCalled()->willReturn($wrongOrderClass);

        $this->shouldThrow($exception)->duringRecalculatePrices($event);
    }

    function it_recalculates_prices_adding_customer_to_the_context(
        GenericEvent $event,
        OrderInterface $order,
        GroupableInterface $customer,
        ArrayCollection $groups
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($order);

        $order->getCustomer()->shouldBeCalled()->willReturn($customer);
        $order->getChannel()->shouldBeCalled()->willReturn(null);
        $order->getItems()->shouldBeCalled()->willReturn(array());
        $order->calculateTotal()->shouldBeCalled();

        $customer->getGroups()->shouldBeCalled()->willReturn($groups);

        $groups->toArray()->shouldBeCalled()->willReturn(array('group1', 'group2'));

        $this->recalculatePrices($event);
    }

    function it_recalculates_prices_adding_only_channels(
        GenericEvent $event,
        OrderInterface $order,
        ChannelInterface $channel
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($order);

        $order->getCustomer()->shouldBeCalled()->willReturn(null);
        $order->getChannel()->shouldBeCalled()->willReturn($channel);
        $order->getItems()->shouldBeCalled()->willReturn(array());
        $order->calculateTotal()->shouldBeCalled();

        $this->recalculatePrices($event);
    }

    function it_recalculates_prices_adding_items_wihtout_adding_customer_or_channel(
        GenericEvent $event,
        OrderInterface $order,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        DelegatingCalculatorInterface $priceCalculator,
        PriceableInterface $variant
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($order);

        $order->getCustomer()->shouldBeCalled()->willReturn(null);
        $order->getChannel()->shouldBeCalled()->willReturn(null);
        $order->getItems()->shouldBeCalled()->willReturn(array($item1, $item2));
        $order->calculateTotal()->shouldBeCalled();

        $item1->isImmutable()->shouldBeCalled()->willReturn(true);
        $item1->getQuantity()->shouldNotBeCalled();
        $item1->setUnitPrice(Argument::type('integer'))->shouldNotBeCalled();
        $item1->getVariant()->shouldNotBeCalled();

        $item2->isImmutable()->shouldBeCalled()->willReturn(false);
        $item2->getQuantity()->shouldBeCalled()->willReturn(5);
        $item2->setUnitPrice(10)->shouldBeCalled();
        $item2->getVariant()->shouldBeCalled()->willReturn($variant);

        $priceCalculator->calculate($variant, array('quantity' => 5))->shouldBeCalled()->willReturn(10);

        $this->recalculatePrices($event);
    }

    function it_recalculates_prices_adding_all_context(
        GenericEvent $event,
        OrderInterface $order,
        OrderItemInterface $item,
        DelegatingCalculatorInterface $priceCalculator,
        GroupableInterface $customer,
        ArrayCollection $groups,
        ChannelInterface $channel,
        PriceableInterface $variant
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($order);

        $order->getCustomer()->shouldBeCalled()->willReturn($customer);
        $order->getChannel()->shouldBeCalled()->willReturn($channel);
        $order->getItems()->shouldBeCalled()->willReturn(array($item));
        $order->calculateTotal()->shouldBeCalled();

        $customer->getGroups()->shouldBeCalled()->willReturn($groups);

        $groups->toArray()->shouldBeCalled()->willReturn(array('group1', 'group2'));

        $item->isImmutable()->shouldBeCalled()->willReturn(false);
        $item->getQuantity()->shouldBeCalled()->willReturn(5);
        $item->setUnitPrice(10)->shouldBeCalled();
        $item->getVariant()->shouldBeCalled()->willReturn($variant);

        $priceCalculator->calculate(
            $variant,
            array(
                'customer' => $customer,
                'groups' => array('group1', 'group2'),
                'channel' => array($channel),
                'quantity' => 5
            )
        )->shouldBeCalled()->willReturn(10);

        $this->recalculatePrices($event);
    }
}
