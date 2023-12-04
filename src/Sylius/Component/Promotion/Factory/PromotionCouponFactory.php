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

namespace Sylius\Component\Promotion\Factory;

use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @implements PromotionCouponFactoryInterface<PromotionCouponInterface>
 */
final class PromotionCouponFactory implements PromotionCouponFactoryInterface
{
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function createNew(): PromotionCouponInterface
    {
        return $this->factory->createNew();
    }

    public function createForPromotion(PromotionInterface $promotion): PromotionCouponInterface
    {
        Assert::true(
            $promotion->isCouponBased(),
            sprintf('Promotion with name %s is not coupon based.', $promotion->getName()),
        );

        /** @var PromotionCouponInterface $coupon */
        $coupon = $this->factory->createNew();
        $coupon->setPromotion($promotion);

        return $coupon;
    }
}
