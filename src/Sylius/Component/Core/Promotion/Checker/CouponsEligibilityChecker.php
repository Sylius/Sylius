<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker;

use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\CouponsEligibilityChecker as BaseCouponsEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CouponsEligibilityChecker extends BaseCouponsEligibilityChecker
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, OrderRepositoryInterface $orderRepository)
    {
        parent::__construct($eventDispatcher);

        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function isCouponEligible(PromotionInterface $promotion, PromotionCouponAwareSubjectInterface $subject)
    {
        $coupon = $subject->getPromotionCoupon();
        if (null === $coupon || $promotion !== $coupon->getPromotion()) {
            return false;
        }

        return $this->isCouponEligibleToLimit($coupon, $subject->getCustomer());
    }

    /**
     * @param CouponInterface $coupon
     * @param CustomerInterface|null $customer
     *
     * @return bool
     */
    private function isCouponEligibleToLimit(CouponInterface $coupon, CustomerInterface $customer = null)
    {
        $couponUsageLimit = $coupon->getPerCustomerUsageLimit();
        if (0 === $couponUsageLimit) {
            return true;
        }

        if (null === $customer) {
            return false;
        }

        $placedOrdersNumber = $this->orderRepository->countByCustomerAndCoupon($customer, $coupon);

        // <= because we need to include the cart orders as well
        return $placedOrdersNumber <= $couponUsageLimit;
    }
}
