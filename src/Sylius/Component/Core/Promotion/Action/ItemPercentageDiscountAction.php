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

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Filter\TaxonFilterInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ItemPercentageDiscountAction extends DiscountAction
{
    /**
     * @var IntegerDistributorInterface
     */
    private $distributor;

    /**
     * @var TaxonFilterInterface
     */
    private $taxonFilter;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param OriginatorInterface $originator
     * @param IntegerDistributorInterface $distributor
     * @param TaxonFilterInterface $taxonFilter
     */
    public function __construct(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        IntegerDistributorInterface $distributor,
        TaxonFilterInterface $taxonFilter
    ) {
        parent::__construct($adjustmentFactory, $originator);

        $this->distributor = $distributor;
        $this->taxonFilter = $taxonFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $filteredItems = $this->taxonFilter->filter($subject->getItems(), $configuration);

        foreach ($filteredItems as $item) {
            $promotionAmount = (int) round($item->getTotal() * $configuration['percentage']);
            $distributedAmounts = $this->distributor->distribute($promotionAmount, $item->getQuantity());

            $this->setUnitsAdjustments($item, $distributedAmounts, $promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        foreach ($subject->getItems() as $item) {
            $this->removeUnitsAdjustment($item, $promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_percentage_discount_configuration';
    }

    /**
     * @param OrderItemInterface $item
     * @param array $distributedAmounts
     * @param PromotionInterface $promotion
     */
    private function setUnitsAdjustments(OrderItemInterface $item, array $distributedAmounts, PromotionInterface $promotion)
    {
        $i = 0;
        foreach ($item->getUnits() as $unit) {
            if (0 === $distributedAmounts[$i]) {
                break;
            }

            $this->addAdjustmentToUnit($unit, $distributedAmounts[$i], $promotion);
            $i++;
        }
    }

    /**X
     * @param OrderItemUnitInterface $unit
     * @param int $amount
     * @param PromotionInterface $promotion
     */
    private function addAdjustmentToUnit(OrderItemUnitInterface $unit, $amount, PromotionInterface $promotion)
    {
        $adjustment = $this->createAdjustment($promotion, AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $adjustment->setAmount(-$amount);

        $unit->addAdjustment($adjustment);
    }

    /**
     * @param OrderItemInterface $item
     * @param PromotionInterface $promotion
     */
    private function removeUnitsAdjustment(OrderItemInterface $item, PromotionInterface $promotion)
    {
        foreach ($item->getUnits() as $unit) {
            $this->removeUnitOrderItemAdjustments($unit, $promotion);
        }
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param PromotionInterface $promotion
     */
    private function removeUnitOrderItemAdjustments(OrderItemUnitInterface $unit, PromotionInterface $promotion)
    {
        foreach ($unit->getAdjustments(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion === $this->originator->getOrigin($adjustment)) {
                $unit->removeAdjustment($adjustment);
            }
        }
    }
}
