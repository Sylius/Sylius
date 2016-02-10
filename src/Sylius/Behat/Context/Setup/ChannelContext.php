<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Test\Services\DefaultStoreDataInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var DefaultStoreDataInterface
     */
    private $defaultFranceChannelFactory;

    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultStoreDataInterface $defaultFranceChannelFactory
     * @param ChannelFactoryInterface $channelFactory
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultStoreDataInterface $defaultFranceChannelFactory,
        ChannelFactoryInterface $channelFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->defaultFranceChannelFactory = $defaultFranceChannelFactory;
        $this->channelFactory = $channelFactory;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @Transform /^channel "([^"]+)"$/
     * @Transform /^"([^"]+)" channel/
     * @Transform :channel
     */
    public function getChannelByName($channelName)
    {
        $channel = $this->channelRepository->findOneBy(['name' => $channelName]);
        if (null === $channel) {
            throw new \InvalidArgumentException('Channel with name "'.$channelName.'" does not exist');
        }

        return $channel;
    }

    /**
     * @Transform /(?:this|that|the) channel/
     */
    public function getLatestChannel()
    {
        return $this->sharedStorage->getCurrentResource('channel');
    }

    /**
     * @Given the store operates on a single channel
     * @Given the store is operating on a single channel
     */
    public function thatStoreIsOperatingOnASingleChannel()
    {
        $defaultData = $this->defaultFranceChannelFactory->create();
        $this->sharedStorage->setClipboard($defaultData);
    }

    /**
     * @Given /^the store operates on (?:a|another) channel named "([^"]+)"$/
     */
    public function theStoreOperatesOnAChannelNamed($channelName)
    {
        $channel = $this->channelFactory->createNamed($channelName);
        $channel->setCode($channelName);

        $this->channelRepository->add($channel);
        $this->sharedStorage->setCurrentResource('channel', $channel);
    }
}
