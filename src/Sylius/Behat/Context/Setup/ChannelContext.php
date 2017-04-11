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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;

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
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var ObjectManager
     */
    private $channelManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultChannelFactoryInterface $unitedStatesChannelFactory
     * @param DefaultChannelFactoryInterface $defaultChannelFactory
     * @param ChannelRepositoryInterface $channelRepository
     * @param ObjectManager $channelManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $unitedStatesChannelFactory,
        DefaultChannelFactoryInterface $defaultChannelFactory,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $channelManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->unitedStatesChannelFactory = $unitedStatesChannelFactory;
        $this->defaultChannelFactory = $defaultChannelFactory;
        $this->channelRepository = $channelRepository;
        $this->channelManager = $channelManager;
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
     * @Given /^the store(?:| also) operates on (?:a|another) channel named "([^"]+)"$/
     * @Given /^the store(?:| also) operates on (?:a|another) channel named "([^"]+)" in "([^"]+)" currency$/
     * @Given the store operates on a channel identified by :code code
     */
    public function theStoreOperatesOnAChannelNamed($channelIdentifier, $currencyCode = null)
    {
        $defaultData = $this->defaultChannelFactory->create($channelIdentifier, $channelIdentifier, $currencyCode);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
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
     * @Given /^(this channel) has contact email set as "([^"]+)"$/
     * @Given /^(this channel) has no contact email set$/
     */
    public function thisChannelHasContactEmailSetAs(ChannelInterface $channel, $contactEmail = null)
    {
        $channel->setContactEmail($contactEmail);
        $this->channelManager->flush();
    }

    /**
     * @Given /^on (this channel) shipping step is skipped if only a single shipping method is available$/
     */
    public function onThisChannelShippingStepIsSkippedIfOnlyASingleShippingMethodIsAvailable(ChannelInterface $channel)
    {
        $channel->setSkippingShippingStepAllowed(true);

        $this->channelManager->flush();
    }

    /**
     * @Given /^on (this channel) payment step is skipped if only a single payment method is available$/
     */
    public function onThisChannelPaymentStepIsSkippedIfOnlyASinglePaymentMethodIsAvailable(
        ChannelInterface $channel
    ) {
        $channel->setSkippingPaymentStepAllowed(true);

        $this->channelManager->flush();
    }

    /**
     * @Given /^on (this channel) account verification is not required$/
     */
    public function onThisChannelAccountVerificationIsNotRequired(ChannelInterface $channel)
    {
        $channel->setAccountVerificationRequired(false);

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
