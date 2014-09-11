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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * Gift card based discount action.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class GiftCardDiscountAction extends DiscountAction implements PromotionActionInterface
{
    /**
     * Order repository.
     *
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface      $adjustmentRepository
     * @param OriginatorInterface      $originator
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(RepositoryInterface $adjustmentRepository, OriginatorInterface $originator, OrderRepositoryInterface $orderRepository)
    {
        parent::__construct($adjustmentRepository, $originator);

        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $coupons = $subject->getPromotionCoupons()->filter(function ($item) {
            /** @var $item CouponInterface */
            return CouponInterface::TYPE_GIFT_CARD === $item->getType() && $item->isValid();
        });

        if (!$coupons->isEmpty()) {
            $this->validateCoupons($coupons);
            $this->processCoupons($subject, $promotion, $coupons);
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
     * Fetch orders that contain specific coupons and validate amount,
     * if it's empty, remove it from current order otherwise reduce
     * amount for same coupon in current order.
     *
     * @param Collection|CouponInterface[] $coupons
     */
    private function validateCoupons(Collection $coupons)
    {
        /** @var $orders OrderInterface[] */
        $orders = $this->orderRepository->findWithCoupons(array(
            'coupons' => $coupons->toArray(),
        ));
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
     * @param OrderInterface               $subject
     * @param PromotionInterface           $promotion
     * @param Collection|CouponInterface[] $coupons
     */
    private function processCoupons(OrderInterface $subject, PromotionInterface $promotion, Collection $coupons)
    {
        foreach ($coupons as $card) {
            if ($subject->getTotal() < $card->getAmount()) {
                $amount = $subject->getTotal();
            } else {
                $amount = $card->getAmount();
            }

            $subject->addAdjustment($this->createAdjustment($promotion, -$amount));
        }
    }
}
