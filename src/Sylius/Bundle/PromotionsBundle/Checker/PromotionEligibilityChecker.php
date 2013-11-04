<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Checker;

use Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;
use Sylius\Bundle\PromotionsBundle\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Checks if promotion rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * @var RuleCheckerRegistryInterface
     */
    protected $registry;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @param RuleCheckerRegistryInterface $registry
     * @param EventDispatcherInterface     $dispatcher
     */
    public function __construct(RuleCheckerRegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        if (false === $this->isEligibleToDates($promotion)) {
            return false;
        }

        if (false === $this->isEligibleToUsageLimit($promotion)) {
            return false;
        }

        if (false === $this->isSubjectEligibleToCoupon($subject, $promotion)) {
            return false;
        }

        foreach ($promotion->getRules() as $rule) {
            if (false === $this->isEligibleToRule($subject, $promotion, $rule)) {
                return false;
            }
        }

        if (false === $this->isCouponEligibleToPromotion($subject, $promotion)) {
            return false;
        }

        return true;
    }

    /**
     * Checks is a promotion is eligible to a subject for a given rule.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     * @param RuleInterface             $rule
     *
     * @return bool
     */
    private function isEligibleToRule(PromotionSubjectInterface $subject, PromotionInterface $promotion, RuleInterface $rule)
    {
        $checker = $this->registry->getChecker($rule->getType());

        if (false === $checker->isEligible($subject, $rule->getConfiguration())) {
            if ($promotion->isCouponBased() && $promotion === $subject->getPromotionCoupon()->getPromotion()) {
                $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($promotion));
            }

            return false;
        }

        return true;
    }

    /**
     * Checks if the current is between promotion limits.
     *
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    private function isEligibleToDates(PromotionInterface $promotion)
    {
        $now = new \DateTime();

        if (null !== $startsAt = $promotion->getStartsAt()) {
            if ($now < $startsAt) {
                return false;
            }
        }

        if (null !== $endsAt = $promotion->getEndsAt()) {
            if ($now > $endsAt) {
                return false;
            }
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
    private function isEligibleToUsageLimit(PromotionInterface $promotion)
    {
        if (null !== $usageLimit = $promotion->getUsageLimit()) {
            if ($promotion->getUsed() >= $usageLimit) {
                return false;
            }
        }

        return true;
    }


    /**
     * Checks if subject's is eligible to promotion coupon.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     *
     * @return bool
     */
    private function isSubjectEligibleToCoupon(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        if ($promotion->isCouponBased()) {
            if (null === $subject->getPromotionCoupon()) {
                return false;
            }

            if ($promotion !== $subject->getPromotionCoupon()->getPromotion()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks is subject's coupon is eligible to promotion.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     *
     * @return bool
     */
    private function isCouponEligibleToPromotion(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        if (!$promotion->hasCoupons()) {
            return true;
        }

        $coupon = $subject->getPromotionCoupon();
        if ($coupon && !$promotion->hasCoupon($coupon)) {
            return false;
        }

        if ($coupon) {
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));
        }

        return true;
    }
}
