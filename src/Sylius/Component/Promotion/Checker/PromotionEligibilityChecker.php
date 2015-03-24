<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker;

use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponsAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Resource\Checker\EligibilityChecker;
use Sylius\Component\Resource\Model\RuleAwareInterface;
use Sylius\Component\Resource\Model\RuleInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Checks if promotion rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PromotionEligibilityChecker extends EligibilityChecker
{
    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, RuleAwareInterface $object)
    {
        $eligible = parent::isEligible($subject, $object);
        if (!$eligible) {
            return false;
        }

        if (!$this->isEligibleToUsageLimit($object)) {
            return false;
        }

        if (!$object->isCouponBased()) {
            return $eligible;
        }

        return $this->areCouponsEligibleForPromotion($subject, $object);
    }

    /**
     * Checks is a promotion is eligible to a subject for a given rule.
     *
     * @param object             $subject
     * @param RuleAwareInterface $object
     * @param RuleInterface      $rule
     *
     * @return bool
     */
    protected function isEligibleToRule($subject, RuleAwareInterface $object, RuleInterface $rule)
    {
        if (parent::isEligibleToRule($subject, $object, $rule)) {
            return true;
        }

        if (!$object->isCouponBased()) {
            return false;
        }

        if ($subject instanceof PromotionCouponAwareSubjectInterface) {
            $coupon = $subject->getPromotionCoupon();
            if (null !== $coupon && $object === $coupon->getPromotion()) {
                $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($object));
            }
        } elseif ($subject instanceof PromotionCouponsAwareSubjectInterface) {
            foreach ($subject->getPromotionCoupons() as $coupon) {
                if ($object === $coupon->getPromotion()) {
                    $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($object));
                }
            }
        }

        return false;
    }

    /**
     * Checks if promotion usage limit has been reached.
     *
     * @param PromotionInterface $object
     *
     * @return bool
     */
    protected function isEligibleToUsageLimit(PromotionInterface $object)
    {
        if (null !== $usageLimit = $object->getUsageLimit()) {
            return $object->getUsed() < $usageLimit;
        }

        return true;
    }

    /**
     * Checks are subject's coupons eligible to promotion.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $object
     *
     * @return bool
     */
    protected function areCouponsEligibleForPromotion(PromotionSubjectInterface $subject, PromotionInterface $object)
    {
        $eligible = false;
        if ($subject instanceof PromotionCouponAwareSubjectInterface) {
            $coupon = $subject->getPromotionCoupon();
            if (null !== $coupon && $object === $coupon->getPromotion()) {
                $eligible = true;
            }
        } elseif ($subject instanceof PromotionCouponsAwareSubjectInterface) {
            foreach ($subject->getPromotionCoupons() as $coupon) {
                if ($object === $coupon->getPromotion()) {
                    $eligible = true;

                    break;
                }
            }
        } else {
            return false;
        }

        if ($eligible) {
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($object));
        }

        return $eligible;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($subject, $object)
    {
        if (!$subject instanceof PromotionSubjectInterface) {
            return false;
        }
        
        if (!$object instanceof PromotionInterface) {
            return false;
        }
        
        return true;
    }
}
