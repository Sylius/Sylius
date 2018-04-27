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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(ChannelRepositoryInterface $channelRepository, EntityManagerInterface $entityManager)
    {
        $this->channelRepository = $channelRepository;
        $this->entityManager = $entityManager;
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

        $this->entityManager->refresh($channels[0]);

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
