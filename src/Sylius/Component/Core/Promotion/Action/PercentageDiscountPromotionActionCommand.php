<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

final class PercentageDiscountPromotionActionCommand extends DiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    public const TYPE = 'order_percentage_discount';

    private ProportionalIntegerDistributorInterface $distributor;

    private UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator;

    public function __construct(
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $this->distributor = $distributor;
        $this->unitsPromotionAdjustmentsApplicator = $unitsPromotionAdjustmentsApplicator;
    }

    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf($subject, OrderInterface::class);

        if (!$this->isSubjectValid($subject)) {
            return false;
        }

        try {
            $this->isConfigurationValid($configuration);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        $promotionAmount = $this->calculateAdjustmentAmount($subject->getPromotionSubjectTotal(), $configuration['percentage']);
        if (0 === $promotionAmount) {
            return false;
        }

        $itemsTotal = [];
        $minimumPrices = [];
        foreach ($subject->getItems() as $orderItem) {
            $itemsTotal[] = $orderItem->getTotal();
            $minimumPrices[] = $orderItem->getVariant()->getChannelPricingForChannel($subject->getChannel())->getMinimumPrice() * $orderItem->getQuantity();
        }

        $splitPromotion = $this->distributor->distribute($itemsTotal, $promotionAmount);
        $distributionData = $this->distributeWithMinimumPrice($splitPromotion, $itemsTotal, $minimumPrices);

        if (array_sum($distributionData['toDistribute']) > 0) {
            $splitPromotion = $this->distributor->distribute($distributionData['toDistribute'], $distributionData['amountLeft']);
            $splitPromotion = array_merge($distributionData['distributedPromotion'], $splitPromotion);
        }

        $this->unitsPromotionAdjustmentsApplicator->apply($subject, $promotion, $splitPromotion);

        return true;
    }

    protected function isConfigurationValid(array $configuration): void
    {
        Assert::keyExists($configuration, 'percentage');
        Assert::greaterThan($configuration['percentage'], 0);
        Assert::lessThanEq($configuration['percentage'], 1);
    }

    private function calculateAdjustmentAmount(int $promotionSubjectTotal, float $percentage): int
    {
        return -1 * (int) round($promotionSubjectTotal * $percentage);
    }
}
