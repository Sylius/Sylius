<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * Gift card based discount action.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Saidul Islam <saidul.04@gmail.com>
 */
class GiftCardDiscountAction extends DiscountAction
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param FactoryInterface         $adjustmentFactory
     * @param OriginatorInterface      $originator
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(FactoryInterface $adjustmentFactory, OriginatorInterface $originator, OrderRepositoryInterface $orderRepository)
    {
        parent::__construct($adjustmentFactory, $originator);

        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $coupons = $subject->getPromotionCoupons()->filter(function (CouponInterface $item) {
            return CouponInterface::TYPE_GIFT_CARD === $item->getType() && $item->isValid();
        });

        if (!$coupons->isEmpty()) {
            $this->validateCoupons($coupons);
            $this->processCoupons($subject, $coupons);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_gift_card_discount_configuration';
    }

    /**
     * @param CouponInterface $coupon
     * @param null|int $amount
     *
     * @return AdjustmentInterface
     */
    protected function createCouponAdjustment(CouponInterface $coupon, $amount = null)
    {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType(AdjustmentInterface::PROMOTION_ADJUSTMENT);
        $adjustment->setLabel($coupon->getType());

        if (null !== $amount) {
            $adjustment->setAmount($amount);
        }

        $this->originator->setOrigin($adjustment, $coupon);

        return $adjustment;
    }

    /**
     * Fetch orders that contain specific coupons and validate amount,
     * if it's empty, remove it from current order otherwise reduce
     * amount for same coupon in current order.
     *
     * @param Collection|CouponInterface[] $coupons
     */
    private function validateCoupons(Collection $coupons)
    {
        /** @var $orders OrderInterface[] */
        $orders = $this->orderRepository->findWithCoupons([
            'coupons' => $coupons->toArray(),
        ]);
        foreach ($orders as $order) {
            foreach ($order->getPromotionCoupons() as $orderCoupon) {
                foreach ($coupons as $coupon) {
                    if ($orderCoupon->getId() !== $coupon->getId()) {
                        continue;
                    }

                    if (0 === $orderCoupon->getAmount()) {
                        $coupons->removeElement($coupon);
                    } else {
                        $coupon->setAmount($orderCoupon->getAmount());
                    }
                }
            }
        }
    }

    /**
     * @param OrderInterface $subject
     * @param Collection|CouponInterface[] $coupons
     */
    private function processCoupons(OrderInterface $subject, Collection $coupons)
    {
        $subject->recalculateAdjustmentsTotal();

        $total = $subject->getTotal();
        foreach ($coupons as $card) {
            $amount = $total < $card->getAmount() ? $total : $card->getAmount();

            $subject->addAdjustment($this->createCouponAdjustment($card, -$amount));
        }
    }
}
