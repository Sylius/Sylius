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

namespace Sylius\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class PromotionApplicator implements PromotionApplicatorInterface
{
    public function __construct(private ServiceRegistryInterface $registry)
    {
    }

    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion): void
    {
        $applyPromotion = false;
        foreach ($promotion->getActions() as $action) {
            $result = $this->getActionCommandByType($action->getType())->execute($subject, $action->getConfiguration(), $promotion);
            $applyPromotion = $applyPromotion || $result;
        }

        if ($applyPromotion) {
            $subject->addPromotion($promotion);
        }
    }

    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion): void
    {
        foreach ($promotion->getActions() as $action) {
            $this->getActionCommandByType($action->getType())->revert($subject, $action->getConfiguration(), $promotion);
        }

        $subject->removePromotion($promotion);
    }

    private function getActionCommandByType(string $type): PromotionActionCommandInterface
    {
        return $this->registry->get($type);
    }
}
