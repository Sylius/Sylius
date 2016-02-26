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
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultChannelFactoryInterface $franceChannelFactory
     * @param DefaultChannelFactoryInterface $defaultChannelFactory
     * @param ChannelFactoryInterface $channelFactory
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $franceChannelFactory,
        DefaultChannelFactoryInterface $defaultChannelFactory,
        ChannelFactoryInterface $channelFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->franceChannelFactory = $franceChannelFactory;
        $this->defaultChannelFactory = $defaultChannelFactory;
        $this->channelFactory = $channelFactory;
        $this->channelRepository = $channelRepository;
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
     */
    public function theStoreOperatesOnAChannelNamed($channelName)
    {
        $channel = $this->channelFactory->createNamed($channelName);
        $channel->setCode($channelName);

        $this->channelRepository->add($channel);
        $this->sharedStorage->set('channel', $channel);
    }
}
