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

use Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

final class FixedDiscountPromotionActionCommand extends DiscountPromotionActionCommand
{
    public const TYPE = 'order_fixed_discount';

    private UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator;

    private MinimumPriceDistributorInterface $minimumPriceDistributor;

    public function __construct(
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        MinimumPriceDistributorInterface $minimumPriceDistributor
    ) {
        $this->unitsPromotionAdjustmentsApplicator = $unitsPromotionAdjustmentsApplicator;
        $this->minimumPriceDistributor = $minimumPriceDistributor;
    }

    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf($subject, OrderInterface::class);

        if (!$this->isSubjectValid($subject)) {
            return false;
        }

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return false;
        }

        try {
            $this->isConfigurationValid($configuration[$channelCode]);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        $promotionAmount = $this->calculateAdjustmentAmount(
            $subject->getPromotionSubjectTotal(),
            $configuration[$channelCode]['amount']
        );

        if (0 === $promotionAmount) {
            return false;
        }

        $splitPromotion = $this->minimumPriceDistributor->distribute($subject->getItems()->toArray(), $promotionAmount, $subject->getChannel());

        $this->unitsPromotionAdjustmentsApplicator->apply($subject, $promotion, $splitPromotion);

        return true;
    }

    protected function isConfigurationValid(array $configuration): void
    {
        Assert::keyExists($configuration, 'amount');
        Assert::integer($configuration['amount']);
    }

    private function calculateAdjustmentAmount(int $promotionSubjectTotal, int $targetPromotionAmount): int
    {
        return -1 * min($promotionSubjectTotal, $targetPromotionAmount);
    }
}
