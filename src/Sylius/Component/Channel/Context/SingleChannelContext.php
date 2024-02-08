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

namespace Sylius\Component\Channel\Context;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Webmozart\Assert\Assert;

final class SingleChannelContext implements ChannelContextInterface
{
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function getChannel(): ChannelInterface
    {
        $channels = $this->channelRepository->findAll();

        if (1 !== count($channels)) {
            throw new ChannelNotFoundException();
        }
        $channel = reset($channels);
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }
}
