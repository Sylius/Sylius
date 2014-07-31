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
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
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
            return false;
        }

        if (!$this->isEligibleToUsageLimit($promotion)) {
            return false;
        }

        if ($promotion->hasRules()) {
            foreach ($promotion->getRules() as $rule) {
                try {
                    if (!$this->isEligibleToRule($subject, $promotion, $rule)) {
                        return false;
                    }
                } catch (UnsupportedTypeException $exception) {
                    continue;
                }
            }
        }

        if (!$subject instanceof PromotionCouponAwareSubjectInterface) {
            return true;
        }

        return $this->isCouponEligibleToPromotion($promotion, $subject->getPromotionCoupon());
    }

    /**
     * Checks is a promotion is eligible to a subject for a given rule.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     * @param RuleInterface             $rule
     *
     * @return Boolean
     */
    protected function isEligibleToRule(PromotionSubjectInterface $subject, PromotionInterface $promotion, RuleInterface $rule)
    {
        $checker = $this->registry->get($rule->getType());

        if (!$checker->isEligible($subject, $rule->getConfiguration())) {
            if ($subject instanceof PromotionCouponAwareSubjectInterface
                && null !== ($coupon = $subject->getPromotionCoupon())
                && $promotion->isCouponBased()
                && $promotion === $coupon->getPromotion()
            ) {
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
     * @return Boolean
     */
    protected function isEligibleToDates(PromotionInterface $promotion)
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
     * @return Boolean
     */
    protected function isEligibleToUsageLimit(PromotionInterface $promotion)
    {
        if (null !== $usageLimit = $promotion->getUsageLimit()) {
            if ($promotion->getUsed() >= $usageLimit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks is subject's coupon is eligible to promotion.
     *
     * @param PromotionInterface   $promotion
     * @param null|CouponInterface $coupon
     *
     * @return Boolean
     */
    protected function isCouponEligibleToPromotion(PromotionInterface $promotion, CouponInterface $coupon = null)
    {
        if (!$promotion->isCouponBased()) {
            return true;
        }

        if (null === $coupon || $promotion !== $coupon->getPromotion()) {
            return false;
        }

        if (null !== $coupon) {
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));
        }

        return true;
    }
}
