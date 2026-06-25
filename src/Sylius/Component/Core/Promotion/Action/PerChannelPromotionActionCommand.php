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
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

final class PerChannelPromotionActionCommand implements PromotionActionCommandInterface
{
    public function __construct(private PromotionActionCommandInterface $decorated)
    {
    }

    /** @param array<string, mixed> $configuration */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $channel = $subject->getChannel();
        if ($channel === null) {
            return false;
        }

        $channelCode = $channel->getCode();
        if (!isset($configuration[$channelCode]) || !is_array($configuration[$channelCode])) {
            return false;
        }

        return $this->decorated->execute($subject, $configuration[$channelCode], $promotion);
    }

    /** @param array<string, mixed> $configuration */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $channel = $subject->getChannel();
        if ($channel === null) {
            return;
        }

        $channelCode = $channel->getCode();
        if (!isset($configuration[$channelCode]) || !is_array($configuration[$channelCode])) {
            return;
        }

        $this->decorated->revert($subject, $configuration[$channelCode], $promotion);
    }
}
