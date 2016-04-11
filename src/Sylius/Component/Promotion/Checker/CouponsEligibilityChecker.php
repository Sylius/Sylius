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
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CouponsEligibilityChecker implements PromotionSubjectEligibilityCheckerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        if (!$subject instanceof PromotionCouponAwareSubjectInterface || null === $subject->getPromotionCoupon()) {
            return false;
        }

        if (!$this->isCouponEligible($promotion, $subject)) {
            $this->eventDispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($promotion));

            return false;
        }

        $this->eventDispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));

        return true;
    }

    /**
     * @param PromotionInterface $promotion
     * @param PromotionCouponAwareSubjectInterface $subject
     *
     * @return bool
     */
    protected function isCouponEligible(PromotionInterface $promotion, PromotionCouponAwareSubjectInterface $subject)
    {
        return $promotion === $subject->getPromotionCoupon()->getPromotion();
    }
}
