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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Webmozart\Assert\Assert;

final class PromotionContext implements Context
{
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
        private PromotionCouponRepositoryInterface $promotionCouponRepository,
    ) {
    }

    /**
     * @Transform /^promotion "([^"]+)"$/
     * @Transform /^"([^"]+)" promotion$/
     * @Transform :promotion
     */
    public function getPromotionByName($promotionName)
    {
        $promotion = $this->promotionRepository->findOneBy(['name' => $promotionName]);

        Assert::notNull(
            $promotion,
            sprintf('Promotion with name "%s" does not exist', $promotionName),
        );

        return $promotion;
    }

    /**
     * @Transform /^coupon "([^"]+)"$/
     * @Transform /^"([^"]+)" coupon$/
     * @Transform :coupon
     */
    public function getPromotionCouponByCode($promotionCouponCode)
    {
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $promotionCouponCode]);

        Assert::notNull(
            $promotionCoupon,
            sprintf('Promotion coupon with code "%s" does not exist', $promotionCouponCode),
        );

        return $promotionCoupon;
    }
}
