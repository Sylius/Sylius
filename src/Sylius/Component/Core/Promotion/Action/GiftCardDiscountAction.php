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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * Gift card based discount action.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GiftCardDiscountAction implements PromotionActionInterface
{
    /**
     * Adjustment repository.
     *
     * @var RepositoryInterface
     */
    protected $adjustmentRepository;

    /**
     * Coupon repository.
     *
     * @var RepositoryInterface
     */
    protected $couponRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $adjustmentRepository
     * @param RepositoryInterface $couponRepository
     */
    public function __construct(RepositoryInterface $adjustmentRepository, RepositoryInterface $couponRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->couponRepository     = $couponRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        /** @var $cards CouponInterface[] */
        $cards = $subject->getPromotionCoupons()->filter(function ($item) {
            return CouponInterface::TYPE_GIFT_CARD === $item->getType() && $item->isValid();
        });

        foreach ($cards as $card) {
            if ($subject->getTotal() < $card->getAmount()) {
                $amount = $subject->getTotal();
            } else {
                $amount = $card->getAmount();
            }

            $adjustment = $this->adjustmentRepository->createNew();
            $adjustment->setAmount(-$amount);
            $adjustment->setLabel(OrderInterface::PROMOTION_ADJUSTMENT);
            $adjustment->setDescription($promotion->getDescription());

            $subject->addAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $subject->removePromotionAdjustments();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_gift_card_discount_configuration';
    }
}
