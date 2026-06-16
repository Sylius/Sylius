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
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class ChannelAwarePromotionApplicator implements PromotionApplicatorInterface, ChannelAwareConfigurationInterface
{
    public function __construct(private ServiceRegistryInterface $registry)
    {
    }

    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion): void
    {
        $applyPromotion = false;
        foreach ($promotion->getActions() as $action) {
            if ($this->isActionSkippedForChannel($action, $subject)) {
                continue;
            }

            $configuration = $this->stripChannelKey($action->getConfiguration());
            $result = $this->getActionCommandByType($action->getType())->execute($subject, $configuration, $promotion);
            $applyPromotion = $applyPromotion || $result;
        }

        if ($applyPromotion) {
            $subject->addPromotion($promotion);
        }
    }

    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion): void
    {
        foreach ($promotion->getActions() as $action) {
            if ($this->isActionSkippedForChannel($action, $subject)) {
                continue;
            }

            $configuration = $this->stripChannelKey($action->getConfiguration());
            $this->getActionCommandByType($action->getType())->revert($subject, $configuration, $promotion);
        }

        $subject->removePromotion($promotion);
    }

    private function isActionSkippedForChannel(PromotionActionInterface $action, PromotionSubjectInterface $subject): bool
    {
        $channels = $action->getConfiguration()[self::CHANNELS_CONFIGURATION_KEY] ?? [];
        if ($channels === []) {
            return false;
        }

        if (!$subject instanceof OrderInterface) {
            return false;
        }

        return !in_array($subject->getChannel()->getCode(), $channels, true);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<string, mixed>
     */
    private function stripChannelKey(array $configuration): array
    {
        unset($configuration[self::CHANNELS_CONFIGURATION_KEY]);

        return $configuration;
    }

    private function getActionCommandByType(string $type): PromotionActionCommandInterface
    {
        return $this->registry->get($type);
    }
}
