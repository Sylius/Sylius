<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @var PromotionCouponRepositoryInterface
     */
    private $promotionCouponRepository;

    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->promotionCouponRepository = $promotionCouponRepository;
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
            sprintf('Promotion with name "%s" does not exist', $promotionName)
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
            sprintf('Promotion coupon with code "%s" does not exist', $promotionCouponCode)
        );

        return $promotionCoupon;
    }
}
