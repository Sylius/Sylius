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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
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
     * @var DefaultChannelFactoryInterface
     */
    private $franceChannelFactory;

    /**
     * @var DefaultChannelFactoryInterface
     */
    private $defaultChannelFactory;

    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var ObjectManager
     */
    private $channelManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultChannelFactoryInterface $franceChannelFactory
     * @param DefaultChannelFactoryInterface $defaultChannelFactory
     * @param ChannelFactoryInterface $channelFactory
     * @param ChannelRepositoryInterface $channelRepository
     * @param ObjectManager $channelManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $franceChannelFactory,
        DefaultChannelFactoryInterface $defaultChannelFactory,
        ChannelFactoryInterface $channelFactory,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $channelManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->franceChannelFactory = $franceChannelFactory;
        $this->defaultChannelFactory = $defaultChannelFactory;
        $this->channelFactory = $channelFactory;
        $this->channelRepository = $channelRepository;
        $this->channelManager = $channelManager;
    }

    /**
     * @Given the store operates on a single channel in "France"
     */
    public function storeOperatesOnASingleChannelInFrance()
    {
        $defaultData = $this->franceChannelFactory->create();
        $this->sharedStorage->setClipboard($defaultData);
    }

    /**
     * @Given the store operates on a single channel
     */
    public function storeOperatesOnASingleChannel()
    {
        $defaultData = $this->defaultChannelFactory->create();
        $this->sharedStorage->setClipboard($defaultData);
    }

    /**
     * @Given /^the store operates on (?:a|another) channel named "([^"]+)"$/
     * @Given the store operates on a channel identified by :code code
     */
    public function theStoreOperatesOnAChannelNamed($channelIdentifier)
    {
        $channel = $this->channelFactory->createNamed($channelIdentifier);
        $channel->setCode($channelIdentifier);

        $this->channelRepository->add($channel);
        $this->sharedStorage->set('channel', $channel);
    }

    /**
     * @Given the channel :channel is enabled
     */
    public function theChannelIsEnabled(ChannelInterface $channel)
    {
        $this->changeChannelState($channel, true);
    }

    /**
     * @Given the channel :channel is disabled
     */
    public function theChannelIsDisabled(ChannelInterface $channel)
    {
        $this->changeChannelState($channel, false);
    }

    /**
     * @param ChannelInterface $channel
     * @param bool $state
     */
    private function changeChannelState(ChannelInterface $channel, $state)
    {
        $channel->setEnabled($state);
        $this->channelManager->flush();
        $this->sharedStorage->set('channel', $channel);
    }
}
