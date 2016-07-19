<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UnitPercentageDiscountAction extends UnitDiscountAction
{
    const TYPE = 'unit_percentage_discount';

    /**
     * @var FilterInterface
     */
    private $priceRangeFilter;

    /**
     * @var FilterInterface
     */
    private $taxonFilter;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param OriginatorInterface $originator
     * @param FilterInterface $priceRangeFilter
     * @param FilterInterface $taxonFilter
     */
    public function __construct(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter
    ) {
        parent::__construct($adjustmentFactory, $originator);

        $this->priceRangeFilter = $priceRangeFilter;
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

        $filteredItems = $this->priceRangeFilter->filter($subject->getItems()->toArray(), $configuration);
        $filteredItems = $this->taxonFilter->filter($filteredItems, $configuration);

        foreach ($filteredItems as $item) {
            $promotionAmount = (int) round($item->getUnitPrice() * $configuration['percentage']);
            $this->setUnitsAdjustments($item, $promotionAmount, $promotion);
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
     * @param int $promotionAmount
     * @param PromotionInterface $promotion
     */
    private function setUnitsAdjustments(OrderItemInterface $item, $promotionAmount, PromotionInterface $promotion)
    {
        foreach ($item->getUnits() as $unit) {
            $this->addAdjustmentToUnit($unit, $promotionAmount, $promotion);
        }
    }
}
