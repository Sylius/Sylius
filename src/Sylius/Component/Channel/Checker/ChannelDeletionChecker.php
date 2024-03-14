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

namespace Sylius\Component\Channel\Checker;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class ChannelDeletionChecker implements ChannelDeletionCheckerInterface
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function isDeletable(ChannelInterface $channel): bool
    {
        if (!$channel->isEnabled()) {
            return true;
        }

        $enabledChannels = $this->channelRepository->findEnabled();

        return count($enabledChannels) > 1;
    }
}
