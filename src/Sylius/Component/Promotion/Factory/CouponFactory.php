<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Factory;

use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponFactory implements CouponFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @param FactoryInterface $factory
     * @param PromotionRepositoryInterface $promotionRepository
     */
    public function __construct(FactoryInterface $factory, PromotionRepositoryInterface $promotionRepository)
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
    public function createForPromotion($promotionId)
    {
        if (null === $promotion = $this->promotionRepository->find($promotionId)) {
            throw new \InvalidArgumentException(sprintf('Promotion with id "%s" does not exist.', $promotionId));
        }

        $coupon = $this->factory->createNew();
        $coupon->setPromotion($promotion);

        return $coupon;
    }
}
