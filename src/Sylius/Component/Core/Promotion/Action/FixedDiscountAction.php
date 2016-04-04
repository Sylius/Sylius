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

use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FixedDiscountAction extends DiscountAction
{
    const TYPE = 'order_fixed_discount';

    /**
     * @var ProportionalIntegerDistributorInterface
     */
    private $distributor;

    /**
     * @var UnitsPromotionAdjustmentsApplicatorInterface
     */
    private $unitsPromotionAdjustmentsApplicator;

    /**
     * {@inheritdoc}
     *
     * @param ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
     */
    public function __construct(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        parent::__construct($adjustmentFactory, $originator);

        $this->distributor = $proportionalIntegerDistributor;
        $this->unitsPromotionAdjustmentsApplicator = $unitsPromotionAdjustmentsApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $promotionAmount = $this->calculateAdjustmentAmount($subject->getPromotionSubjectTotal(), $configuration['amount']);

        $itemsTotals = [];
        foreach ($subject->getItems() as $item) {
            $itemsTotals[] = $item->getTotal();
        }

        $splitPromotion = $this->distributor->distribute($subject->getPromotionSubjectTotal(), $itemsTotals, $promotionAmount);
        $this->unitsPromotionAdjustmentsApplicator->apply($subject, $promotion, $splitPromotion);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_fixed_discount_configuration';
    }

    /**
     * @param int $promotionSubjectTotal
     * @param int $targetPromotionAmount
     *
     * @return int
     */
    private function calculateAdjustmentAmount($promotionSubjectTotal, $targetPromotionAmount)
    {
        return -1 * min($promotionSubjectTotal, $targetPromotionAmount);
    }
}
