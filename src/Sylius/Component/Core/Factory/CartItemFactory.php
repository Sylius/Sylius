<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Variation\Resolver\VariantResolverInterface;

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
     * @var VariantResolverInterface
     */
    private $variantResolver;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param VariantResolverInterface $variantResolver
     */
    public function __construct(FactoryInterface $decoratedFactory, VariantResolverInterface $variantResolver)
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
}
