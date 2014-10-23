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
use Sylius\Component\Core\Model\UserAwareInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\PromotionEligibilityChecker as BasePromotionEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponsAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PromotionEligibilityChecker extends BasePromotionEligibilityChecker
{
    /**
     * @var OrderRepositoryInterface
     */
    private $subjectRepository;

    /**
     * @param OrderRepositoryInterface $subjectRepository
     * @param ServiceRegistryInterface $registry
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(OrderRepositoryInterface $subjectRepository, ServiceRegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($registry, $dispatcher);

        $this->subjectRepository = $subjectRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function areCouponsEligibleForPromotion(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        if (!$subject instanceof UserAwareInterface) {
            return false;
        }

        $eligible = false;

        // Check to see if there is a per user usage limit on coupon
        if ($subject instanceof PromotionCouponAwareSubjectInterface) {
            $coupon = $subject->getPromotionCoupon();
            if (null !== $coupon && $promotion === $coupon->getPromotion()) {
                $eligible = $this->isCouponEligibleToLimit($coupon, $promotion, $subject->getUser());
            }
        } elseif ($subject instanceof PromotionCouponsAwareSubjectInterface) {
            foreach ($subject->getPromotionCoupons() as $coupon) {
                if ($promotion === $coupon->getPromotion()) {
                    $eligible = $this->isCouponEligibleToLimit($coupon, $promotion, $subject->getUser());

                    break;
                }
            }
        } else {
            return false;
        }

        if ($eligible) {
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));
        }

        return $eligible;
    }

    private function isCouponEligibleToLimit(CouponInterface $coupon, PromotionInterface $promotion, UserInterface $user = null)
    {
        if (!$coupon instanceof CouponInterface) {
            return true;
        }

        if (0 === $coupon->getPerUserUsageLimit()) {
            return true;
        }

        if (null === $user) {
            return true;
        }

        return $this->isCouponEligible($coupon, $promotion, $user);
    }

    private function isCouponEligible(CouponInterface $coupon, PromotionInterface $promotion, UserInterface $user)
    {
        $countPlacedOrders = $this->subjectRepository->countByUserAndCoupon($user, $coupon);

        // <= because we need to include the cart orders as well
        if ($countPlacedOrders <= $coupon->getPerUserUsageLimit()) {
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));

            return true;
        }

        return false;
    }
}
