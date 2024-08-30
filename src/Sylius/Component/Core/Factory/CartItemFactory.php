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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of OrderItemInterface
 *
 * @implements CartItemFactoryInterface<T>
 */
final class CartItemFactory implements CartItemFactoryInterface
{
    public function __construct(private FactoryInterface $decoratedFactory, private ProductVariantResolverInterface $variantResolver)
    {
    }

    public function createNew(): OrderItemInterface
    {
        return $this->decoratedFactory->createNew();
    }

    public function createForProduct(ProductInterface $product): OrderItemInterface
    {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->createNew();
        $variant = $this->variantResolver->getVariant($product);
        Assert::nullOrIsInstanceOf($variant, ProductVariantInterface::class);
        $cartItem->setVariant($variant);

        return $cartItem;
    }

    public function createForCart(OrderInterface $order): OrderItemInterface
    {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->createNew();
        $cartItem->setOrder($order);

        return $cartItem;
    }
}
