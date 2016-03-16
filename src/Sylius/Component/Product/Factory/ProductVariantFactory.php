<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantFactory implements ProductVariantFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @param FactoryInterface $factory
     * @param RepositoryInterface $productRepository
     */
    public function __construct(FactoryInterface $factory, RepositoryInterface $productRepository)
    {
        $this->factory = $factory;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createForProductWithId($id)
    {
        $product = $this->productRepository->find($id);
        if (null === $product) {
            throw new \InvalidArgumentException(sprintf('Product with id "%s" does not exist.', $id));
        }

        $variant = $this->createNew();
        $variant->setProduct($product);

        return $variant;
    }
}
