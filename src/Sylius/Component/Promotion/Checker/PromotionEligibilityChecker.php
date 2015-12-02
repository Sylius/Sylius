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

use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponsAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Checks if promotion rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PromotionEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param ServiceRegistryInterface $registry
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ServiceRegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        if (!$this->isEligibleToDates($promotion)) {
            return 0;
        }

        if (!$this->isEligibleToUsageLimit($promotion)) {
            return 0;
        }

        $eligible = 1;
        $eligibleRules = 0;
        if ($promotion->hasRules()) {
            foreach ($promotion->getRules() as $rule) {
                try {
                    if (!$this->isEligibleToRule($subject, $promotion, $rule)) {
                        return 0;
                    }

                    ++$eligibleRules;
                } catch (UnsupportedTypeException $exception) {
                    if (0 === $eligibleRules) {
                        $eligible = 0;
                    }

                    continue;
                }
            }
        }

        if (!$promotion->isCouponBased()) {
            return $eligible;
        }

        return $this->areCouponsEligibleForPromotion($subject, $promotion);
    }

    /**
     * Checks is a promotion is eligible to a subject for a given rule.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     * @param RuleInterface             $rule
     *
     * @return int
     */
    protected function isEligibleToRule(PromotionSubjectInterface $subject, PromotionInterface $promotion, RuleInterface $rule)
    {
        $checker = $this->registry->get($rule->getType());
        if ($eligible = $checker->isEligible($subject, $rule->getConfiguration())) {
            return $eligible;
        }

        if (!$promotion->isCouponBased()) {
            return $eligible;
        }

        if ($subject instanceof PromotionCouponAwareSubjectInterface) {
            $coupon = $subject->getPromotionCoupon();
            if (null !== $coupon && $promotion === $coupon->getPromotion()) {
                $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($promotion));
            }
        } elseif ($subject instanceof PromotionCouponsAwareSubjectInterface) {
            foreach ($subject->getPromotionCoupons() as $coupon) {
                if ($promotion === $coupon->getPromotion()) {
                    $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($promotion));
                }
            }
        }

        return $eligible;
    }

    /**
     * Checks if the current is between promotion limits.
     *
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    protected function isEligibleToDates(PromotionInterface $promotion)
    {
        if (null !== $startsAt = $promotion->getStartsAt()) {
            return new \DateTime() > $startsAt;
        }

        if (null !== $endsAt = $promotion->getEndsAt()) {
            return new \DateTime() < $endsAt;
        }

        return true;
    }

    /**
     * Checks if promotion usage limit has been reached.
     *
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    protected function isEligibleToUsageLimit(PromotionInterface $promotion)
    {
        if (null !== $usageLimit = $promotion->getUsageLimit()) {
            return $usageLimit > $promotion->getUsed();
        }

        return true;
    }

    /**
     * Checks are subject's coupons eligible to promotion.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     *
     * @return int
     */
    protected function areCouponsEligibleForPromotion(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        $eligible = 0;
        if ($subject instanceof PromotionCouponAwareSubjectInterface) {
            $coupon = $subject->getPromotionCoupon();
            if (null !== $coupon && $promotion === $coupon->getPromotion()) {
                $eligible = 1;
            }
        } elseif ($subject instanceof PromotionCouponsAwareSubjectInterface) {
            foreach ($subject->getPromotionCoupons() as $coupon) {
                if ($promotion === $coupon->getPromotion()) {
                    $eligible = 1;

                    break;
                }
            }
        } else {
            return 0;
        }

        if ($eligible) {
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));
        }

        return $eligible;
    }
}
