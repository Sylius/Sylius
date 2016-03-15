<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class PromotionContext implements Context
{
    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @var RepositoryInterface
     */
    private $couponRepository;

    /**
     * @param PromotionRepositoryInterface $promotionRepository
     * @param RepositoryInterface $couponRepository
     */
    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        RepositoryInterface $couponRepository
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @Transform /^promotion "([^"]+)"$/
     * @Transform /^"([^"]+)" promotion$/
     * @Transform :promotion
     */
    public function getPromotionByName($promotionName)
    {
        $promotion = $this->promotionRepository->findOneBy(['name' => $promotionName]);
        if (null === $promotion) {
            throw new \InvalidArgumentException(
                sprintf('Promotion with name "%s" does not exist in the promotion repository.', $promotionName)
            );
        }

        return $promotion;
    }

    /**
     * @Transform /^coupon "([^"]+)"$/
     * @Transform /^"([^"]+)" coupon$/
     * @Transform :coupon
     */
    public function getCouponByCode($couponCode)
    {
        $coupon = $this->couponRepository->findOneBy(['code' => $couponCode]);
        if (null === $coupon) {
            throw new \InvalidArgumentException(
                sprintf('Coupon with code "%s" does not exist in the coupon repository.', $couponCode)
            );
        }

        return $coupon;
    }
}
