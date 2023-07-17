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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\OrderShippingMethodAvailable;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\InvalidArgumentException;

final class OrderShippingMethodAvailableValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->initialize($context);
    }

    function it_throws_an_exception_when_constraint_is_not_order_shipping_method_available_instance(Constraint $constraint): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('validate', [null, $constraint]);
    }

    function it_throws_an_exception_when_value_is_not_an_order_instance(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('validate', [null, new OrderShippingMethodAvailable()]);
    }

    function it_adds_a_violation_for_every_not_available_shipping_method_attached_to_the_order(
        ExecutionContextInterface $context,
        ChannelInterface $channel,
        OrderInterface $value,
        ShipmentInterface $shipmentOne,
        ShipmentInterface $shipmentTwo,
        ShippingMethodInterface $shippingMethodOne,
        ShippingMethodInterface $shippingMethodTwo,
        Collection $channelsCollectionOne,
        Collection $channelsCollectionTwo,
    ): void {
        $value->getShipments()->willReturn(new ArrayCollection([$shipmentOne->getWrappedObject(), $shipmentTwo->getWrappedObject()]));
        $value->getChannel()->willReturn($channel);

        $shipmentOne->getMethod()->willReturn($shippingMethodOne);
        $shipmentTwo->getMethod()->willReturn($shippingMethodTwo);

        $shippingMethodOne->isEnabled()->willReturn(false);
        $shippingMethodTwo->isEnabled()->willReturn(true);

        $shippingMethodOne->getChannels()->willReturn($channelsCollectionOne);
        $shippingMethodTwo->getChannels()->willReturn($channelsCollectionTwo);

        $shippingMethodOne->getName()->willReturn('Shipping method one');
        $shippingMethodTwo->getName()->willReturn('Shipping method two');

        $channelsCollectionOne->contains($channel)->willReturn(true);
        $channelsCollectionTwo->contains($channel)->willReturn(false);

        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method one'])->shouldBeCalled();
        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method two'])->shouldBeCalled();

        $this->validate($value, new OrderShippingMethodAvailable());
    }

    function it_does_not_add_violation_if_all_shipping_methods_are_available(
        ExecutionContextInterface $context,
        ChannelInterface $channel,
        OrderInterface $value,
        ShipmentInterface $shipmentOne,
        ShipmentInterface $shipmentTwo,
        ShippingMethodInterface $shippingMethodOne,
        ShippingMethodInterface $shippingMethodTwo,
        Collection $channelsCollectionOne,
        Collection $channelsCollectionTwo,
    ): void {
        $value->getShipments()->willReturn(new ArrayCollection([$shipmentOne->getWrappedObject(), $shipmentTwo->getWrappedObject()]));
        $value->getChannel()->willReturn($channel);

        $shipmentOne->getMethod()->willReturn($shippingMethodOne);
        $shipmentTwo->getMethod()->willReturn($shippingMethodTwo);

        $shippingMethodOne->isEnabled()->willReturn(true);
        $shippingMethodTwo->isEnabled()->willReturn(true);

        $shippingMethodOne->getChannels()->willReturn($channelsCollectionOne);
        $shippingMethodTwo->getChannels()->willReturn($channelsCollectionTwo);

        $shippingMethodOne->getName()->willReturn('Shipping method one');
        $shippingMethodTwo->getName()->willReturn('Shipping method two');

        $channelsCollectionOne->contains($channel)->willReturn(true);
        $channelsCollectionTwo->contains($channel)->willReturn(true);

        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method one'])->shouldNotBeCalled();
        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method two'])->shouldNotBeCalled();

        $this->validate($value, new OrderShippingMethodAvailable());
    }
}
