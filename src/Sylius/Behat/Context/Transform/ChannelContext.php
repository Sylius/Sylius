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
     * @Transform :channel
     */
    public function getChannelByName($channelName)
    {
        $channel = $this->channelRepository->findOneByName($channelName);
        if (null === $channel) {
            throw new \InvalidArgumentException('Channel with name "'.$channelName.'" does not exist');
        }

        return $channel;
    }
}
