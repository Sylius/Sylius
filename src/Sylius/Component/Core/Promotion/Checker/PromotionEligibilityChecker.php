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

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\PromotionEligibilityChecker as BasePromotionEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;

class PromotionEligibilityChecker extends BasePromotionEligibilityChecker
{
    /**
     * @var OrderRepositoryInterface
     */
    private $subjectRepository;

    /**
     * @param RuleCheckerRegistryInterface $registry
     * @param EventDispatcherInterface     $dispatcher
     */
    public function __construct(OrderRepositoryInterface $subjectRepository, ServiceRegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($registry, $dispatcher);

        $this->subjectRepository = $subjectRepository;
    }

    /**
     * Checks is subject's coupon is eligible to promotion.
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     *
     * @return bool
     */
    protected function isCouponEligibleToPromotion(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        if ($promotion->isCouponBased()) {
            $coupon = $subject->getPromotionCoupon();

            if (null === $coupon || $promotion !== $coupon->getPromotion()) {
                return false;
            }

            // Check to see if there is a per user usage limit on coupon
            if ($coupon->getPerUserUsageLimit() > 0) {
                // The user must be assigned to order
                if ($subject instanceof OrderInterface
                    && null !== $subject->getUser()
                    && $this->subjectRepository instanceof OrderRepository) {

                    $numberOfPlacedOrders = $this->subjectRepository->countByUserAndCoupon($subject->getUser(), $coupon);

                     // <= because we need to include the cart orders as well
                    if ($numberOfPlacedOrders <= $coupon->getPerUserUsageLimit()) {
                        $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));

                        return true;
                    }
                }

                $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent($promotion));

                return false;

            }

            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, new GenericEvent($promotion));
        }

        return true;
    }

}
