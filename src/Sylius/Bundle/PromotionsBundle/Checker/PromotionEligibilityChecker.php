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
use Sylius\Bundle\PromotionsBundle\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
     * @param EventDispatcher $dispatcher
     */
    public function __construct(RuleCheckerRegistryInterface $registry, EventDispatcher $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, PromotionInterface $promotion)
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

        if (null !== $usageLimit = $promotion->getUsageLimit()) {
            if ($promotion->getUsed() >= $usageLimit) {
                return false;
            }
        }

        if ($promotion->isCouponBased()) {
            if (null === $subject->getPromotionCoupon()) {
                return false;
            }

            if ($promotion !== $subject->getPromotionCoupon()->getPromotion()) {
                return false;
            }
        }

        foreach ($promotion->getRules() as $rule) {
            $checker = $this->registry->getChecker($rule->getType());

            if (false === $checker->isEligible($subject, $rule->getConfiguration())) {
                if ($promotion === $subject->getPromotionCoupon()->getPromotion()) {
                    $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($promotion));
                }
                return false;
            }
        }

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
