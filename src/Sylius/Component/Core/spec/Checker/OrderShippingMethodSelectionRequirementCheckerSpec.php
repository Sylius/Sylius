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

namespace spec\Sylius\Component\Core\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderShippingMethodSelectionRequirementCheckerSpec extends ObjectBehavior
{
    function let(ShippingMethodsResolverInterface $shippingMethodsResolver)
    {
        $this->beConstructedWith($shippingMethodsResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderShippingMethodSelectionRequirementChecker::class);
    }

    function it_implements_order_shipping_necessity_checker_interface()
    {
        $this->shouldImplement(OrderShippingMethodSelectionRequirementCheckerInterface::class);
    }

    function it_says_that_shipping_method_do_not_have_to_be_selected_if_none_of_variants_from_order_requires_shipping(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);

        $firstItem->getVariant()->willReturn($firstProductVariant);
        $secondItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(false);

        $order->hasShipments()->willReturn(false);

        $this->isShippingMethodSelectionRequired($order)->shouldReturn(false);
    }

    function it_says_that_shipping_method_do_not_have_to_be_selected_if_order_variants_require_shipping_but_there_is_only_one_shipping_method_available(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ShippingMethodsResolverInterface $shippingMethodsResolver
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);

        $firstItem->getVariant()->willReturn($firstProductVariant);
        $secondItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(true);

        $order->hasShipments()->willReturn(true);

        $order->getChannel()->willReturn($channel);
        $channel->isSkippingShippingStepAllowed()->willReturn(true);

        $order->getShipments()->willReturn([$shipment]);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $this->isShippingMethodSelectionRequired($order)->shouldReturn(false);
    }

    function it_says_that_shipping_method_have_to_be_selected_if_order_variants_require_shipping_and_order_has_not_shipments_yet(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);

        $firstItem->getVariant()->willReturn($firstProductVariant);
        $secondItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(true);

        $order->hasShipments()->willReturn(false);

        $this->isShippingMethodSelectionRequired($order)->shouldReturn(true);
    }

    function it_says_that_shipping_method_have_to_be_selected_if_order_variants_require_shipping_and_channel_does_not_allow_to_skip_shipping_step(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);

        $firstItem->getVariant()->willReturn($firstProductVariant);
        $secondItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(true);

        $order->hasShipments()->willReturn(true);

        $order->getChannel()->willReturn($channel);
        $channel->isSkippingShippingStepAllowed()->willReturn(false);

        $this->isShippingMethodSelectionRequired($order)->shouldReturn(true);
    }

    function it_says_that_shipping_method_have_to_be_selected_if_order_variants_require_shipping_and_there_is_more_than_one_shipping_method_available(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodsResolverInterface $shippingMethodsResolver
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);

        $firstItem->getVariant()->willReturn($firstProductVariant);
        $secondItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(true);

        $order->hasShipments()->willReturn(true);

        $order->getChannel()->willReturn($channel);
        $channel->isSkippingShippingStepAllowed()->willReturn(true);

        $order->getShipments()->willReturn([$shipment]);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$firstShippingMethod, $secondShippingMethod]);

        $this->isShippingMethodSelectionRequired($order)->shouldReturn(true);
    }
}
