<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker\Eligibility;

use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CouponsEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion)
    {
        if (!$promotionSubject instanceof OrderInterface) {
            return false;
        }

        $coupon = $promotionSubject->getPromotionCoupon();
        if (!$coupon instanceof CouponInterface) {
            return false;
        }

        if ($promotion !== $coupon->getPromotion()) {
            return false;
        }

        $couponUsageLimit = $coupon->getPerCustomerUsageLimit();
        if (0 === $couponUsageLimit) {
            return true;
        }

        $customer = $promotionSubject->getCustomer();
        if (null === $customer) {
            return false;
        }

        $placedOrdersNumber = $this->orderRepository->countByCustomerAndCoupon($customer, $coupon);

        // <= because we need to include the cart orders as well
        return $placedOrdersNumber <= $couponUsageLimit;
    }
}
