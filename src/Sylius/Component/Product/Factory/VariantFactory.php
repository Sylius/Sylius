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
class VariantFactory implements VariantFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RepositoryInterface
     */
    private $promotionRepository;

    /**
     * @param FactoryInterface $factory
     * @param RepositoryInterface $promotionRepository
     */
    public function __construct(FactoryInterface $factory, RepositoryInterface $promotionRepository)
    {
        $this->factory = $factory;
        $this->promotionRepository = $promotionRepository;
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
    public function createForProduct($promotionId)
    {
        if (null === $promotion = $this->promotionRepository->find($promotionId)) {
            throw new \InvalidArgumentException(sprintf('Product with id "%s" does not exist.', $promotionId));
        }

        $coupon = $this->factory->createNew();
        $coupon->setProduct($promotion);

        return $coupon;
    }
}
