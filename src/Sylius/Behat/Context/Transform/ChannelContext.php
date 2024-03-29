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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    /**
     * @Transform /^channel "([^"]+)"$/
     * @Transform /^"([^"]+)" channel/
     * @Transform /^channel to "([^"]+)"$/
     * @Transform :channel
     */
    public function getChannelByName(string $channelName)
    {
        $channels = $this->channelRepository->findByName($channelName);

        Assert::eq(
            count($channels),
            1,
            sprintf('%d channels has been found with name "%s".', count($channels), $channelName),
        );

        return $channels[0];
    }

    /**
     * @Transform all channels
     *
     * @return array<ChannelInterface>
     */
    public function getAllChannels(): array
    {
        return $this->channelRepository->findAll();
    }
}
