<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CartItemFactory implements CartItemFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param ProductVariantResolverInterface $variantResolver
     */
    public function __construct(FactoryInterface $decoratedFactory, ProductVariantResolverInterface $variantResolver)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->variantResolver = $variantResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createForProduct(ProductInterface $product)
    {
        $cartItem = $this->createNew();
        $cartItem->setVariant($this->variantResolver->getVariant($product));

        return $cartItem;
    }

    /**
     * {@inheritdoc}
     */
    public function createForCart(OrderInterface $order)
    {
        /** @var OrderItem $cartItem */
        $cartItem = $this->createNew();
        $cartItem->setOrder($order);

        return $cartItem;
    }
}
