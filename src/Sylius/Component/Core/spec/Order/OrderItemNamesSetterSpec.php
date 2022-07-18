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

namespace spec\Sylius\Component\Core\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Order\OrderItemNamesSetterInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;

final class OrderItemNamesSetterSpec extends ObjectBehavior
{
    function it_implements_order_item_names_setter_interface(): void
    {
        $this->shouldImplement(OrderItemNamesSetterInterface::class);
    }

    function it_sets_product_and_product_variant_names_on_order_items(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
        ProductVariantTranslationInterface $variantTranslation,
        ProductInterface $product,
        ProductTranslationInterface $productTranslation,
    ): void {
        $order->getLocaleCode()->willReturn('en_US');
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($variant);

        $variant->getProduct()->willReturn($product);
        $variant->getTranslation('en_US')->willReturn($variantTranslation);
        $variantTranslation->getName()->willReturn('Variant name');

        $product->getTranslation('en_US')->willReturn($productTranslation);
        $productTranslation->getName()->willReturn('Product name');

        $orderItem->setVariantName('Variant name')->shouldBeCalled();
        $orderItem->setProductName('Product name')->shouldBeCalled();

        $this->__invoke($order);
    }
}
