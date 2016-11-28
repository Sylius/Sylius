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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

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
    private $unitedStatesChannelFactory;

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
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultChannelFactoryInterface $unitedStatesChannelFactory
     * @param DefaultChannelFactoryInterface $defaultChannelFactory
     * @param ChannelFactoryInterface $channelFactory
     * @param ChannelRepositoryInterface $channelRepository
     * @param ObjectManager $channelManager
     * @param CurrencyStorageInterface $currencyStorage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $unitedStatesChannelFactory,
        DefaultChannelFactoryInterface $defaultChannelFactory,
        ChannelFactoryInterface $channelFactory,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $channelManager,
        CurrencyStorageInterface $currencyStorage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->unitedStatesChannelFactory = $unitedStatesChannelFactory;
        $this->defaultChannelFactory = $defaultChannelFactory;
        $this->channelFactory = $channelFactory;
        $this->channelRepository = $channelRepository;
        $this->channelManager = $channelManager;
        $this->currencyStorage = $currencyStorage;
    }

    /**
     * @Given the store operates on a single channel in "United States"
     */
    public function storeOperatesOnASingleChannelInUnitedStates()
    {
        $defaultData = $this->unitedStatesChannelFactory->create();

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given the store operates on a single channel in the "United States" named :channelIdentifier
     */
    public function storeOperatesOnASingleChannelInTheUnitedStatesNamed($channelIdentifier)
    {
        $defaultData = $this->unitedStatesChannelFactory->create($channelIdentifier, $channelIdentifier);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given the store operates on a single channel
     * @Given the store operates on a single channel in :currencyCode currency
     */
    public function storeOperatesOnASingleChannel($currencyCode = null)
    {
        $defaultData = $this->defaultChannelFactory->create(null, null, $currencyCode);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given /^the store operates on (?:a|another) channel named "([^"]+)"$/
     * @Given /^the store operates on (?:a|another) channel named "([^"]+)" in "([^"]+)" currency$/
     * @Given the store operates on a channel identified by :code code
     */
    public function theStoreOperatesOnAChannelNamed($channelIdentifier, $currencyCode = null)
    {
        $defaultData = $this->defaultChannelFactory->create($channelIdentifier, $channelIdentifier, $currencyCode);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
        $this->currencyStorage->set($defaultData['channel'], $defaultData['currency']->getCode());
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
     * @Given the channel :channel has been disabled
     */
    public function theChannelIsDisabled(ChannelInterface $channel)
    {
        $this->changeChannelState($channel, false);
    }

    /**
     * @Given channel :channel has been deleted
     */
    public function iChannelHasBeenDeleted(ChannelInterface $channel)
    {
        $this->channelRepository->remove($channel);
    }

    /**
     * @Given /^(its) default tax zone is (zone "([^"]+)")$/
     */
    public function itsDefaultTaxRateIs(ChannelInterface $channel, ZoneInterface $defaultTaxZone)
    {
        $channel->setDefaultTaxZone($defaultTaxZone);
        $this->channelManager->flush();
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
