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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Resolver\VariantResolverInterface;
use Webmozart\Assert\Assert;

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
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var VariantResolverInterface
     */
    private $variantResolver;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param RepositoryInterface $productRepository
     * @param VariantResolverInterface $variantResolver
     */
    public function __construct(
        FactoryInterface $decoratedFactory,
        RepositoryInterface $productRepository,
        VariantResolverInterface $variantResolver
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->productRepository = $productRepository;
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
    public function createForProductWithId($id)
    {
        $product = $this->productRepository->find($id);

        Assert::notNull($product, sprintf('Product with id "%s" does not exist.', $id));

        $cartItem = $this->createNew();
        $cartItem->setVariant($this->variantResolver->getVariant($product));

        return $cartItem;
    }
}
