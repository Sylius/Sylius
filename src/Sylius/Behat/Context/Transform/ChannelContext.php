<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ChannelContext implements Context
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * @Transform /^channel "([^"]+)"$/
     * @Transform /^"([^"]+)" channel/
     * @Transform /^channel to "([^"]+)"$/
     * @Transform :channel
     */
    public function getChannelByName($channelName)
    {
        $channels = $this->channelRepository->findByName($channelName);

        Assert::eq(
            count($channels),
            1,
            sprintf('%d channels has been found with name "%s".', count($channels), $channelName)
        );

        return $channels[0];
    }

    /**
     * @Transform all channels
     */
    public function getAllChannels()
    {
        return $this->channelRepository->findAll();
    }
}
