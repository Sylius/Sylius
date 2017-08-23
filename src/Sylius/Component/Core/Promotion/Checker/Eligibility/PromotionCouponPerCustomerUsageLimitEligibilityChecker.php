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

namespace Sylius\Component\Core\Promotion\Checker\Eligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface as CorePromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionCouponPerCustomerUsageLimitEligibilityChecker implements PromotionCouponEligibilityCheckerInterface
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
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionCouponInterface $promotionCoupon): bool
    {
        if (!$promotionSubject instanceof OrderInterface) {
            return true;
        }

        if (!$promotionCoupon instanceof CorePromotionCouponInterface) {
            return true;
        }

        $perCustomerUsageLimit = $promotionCoupon->getPerCustomerUsageLimit();
        if ($perCustomerUsageLimit === null) {
            return true;
        }

        $customer = $promotionSubject->getCustomer();
        if ($customer === null || $customer->getId() === null) {
            return true;
        }

        $placedOrdersNumber = $this->orderRepository->countByCustomerAndCoupon($customer, $promotionCoupon);

        return $placedOrdersNumber < $perCustomerUsageLimit;
    }
}
