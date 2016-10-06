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

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PromotionCouponFactory implements PromotionCouponFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
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
    public function createForPromotion(PromotionInterface $promotion)
    {
        Assert::true(
            $promotion->isCouponBased(),
            sprintf('Promotion with name %s is not coupon based.', $promotion->getName())
        );

        $coupon = $this->factory->createNew();
        $coupon->setPromotion($promotion);

        return $coupon;
    }
}
