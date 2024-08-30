<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Factory\FactoryInterface;

final class UnitFixedDiscountPromotionActionCommand extends UnitDiscountPromotionActionCommand
{
    public const TYPE = 'unit_fixed_discount';

    public function __construct(
        FactoryInterface $adjustmentFactory,
        private FilterInterface $priceRangeFilter,
        private FilterInterface $taxonFilter,
        private FilterInterface $productFilter,
    ) {
        parent::__construct($adjustmentFactory);
    }

    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return false;
        }

        $amount = $configuration[$channelCode]['amount'];
        if (0 === $amount) {
            return false;
        }

        $filteredItems = $this->priceRangeFilter->filter(
            $subject->getItems()->toArray(),
            array_merge(['channel' => $subject->getChannel()], $configuration[$channelCode]),
        );
        $filteredItems = $this->taxonFilter->filter($filteredItems, $configuration[$channelCode]);
        $filteredItems = $this->productFilter->filter($filteredItems, $configuration[$channelCode]);

        if (empty($filteredItems)) {
            return false;
        }

        foreach ($filteredItems as $item) {
            $this->setUnitsAdjustments($item, $amount, $promotion);
        }

        return true;
    }

    private function setUnitsAdjustments(OrderItemInterface $item, int $amount, PromotionInterface $promotion): void
    {
        /** @var OrderItemUnitInterface $unit */
        foreach ($item->getUnits() as $unit) {
            $this->addAdjustmentToUnit(
                $unit,
                min($unit->getTotal(), $amount),
                $promotion,
            );
        }
    }
}
