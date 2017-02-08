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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Scope as CoreScope;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShippingContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var ShippingMethodExampleFactory
     */
    private $shippingMethodExampleFactory;

    /**
     * @var FactoryInterface
     */
    private $shippingMethodTranslationFactory;

    /**
     * @var ObjectManager
     */
    private $shippingMethodManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param RepositoryInterface $zoneRepository
     * @param ShippingMethodExampleFactory $shippingMethodExampleFactory
     * @param FactoryInterface $shippingMethodTranslationFactory
     * @param ObjectManager $shippingMethodManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        RepositoryInterface $zoneRepository,
        ShippingMethodExampleFactory $shippingMethodExampleFactory,
        FactoryInterface $shippingMethodTranslationFactory,
        ObjectManager $shippingMethodManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->zoneRepository = $zoneRepository;
        $this->shippingMethodExampleFactory = $shippingMethodExampleFactory;
        $this->shippingMethodTranslationFactory = $shippingMethodTranslationFactory;
        $this->shippingMethodManager = $shippingMethodManager;
    }

    /**
     * @Given the store ships everything for free within the :zone zone
     */
    public function storeShipsEverythingForFree(ZoneInterface $zone)
    {
        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => 'Free',
            'enabled' => true,
            'zone' => $zone,
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $this->getConfigurationByChannels([$this->sharedStorage->get('channel')]),
            ],
        ]));
    }

    /**
     * @Given the store ships everywhere for free
     */
    public function theStoreShipsEverywhereForFree()
    {
        /** @var ZoneInterface $zone */
        foreach ($this->zoneRepository->findBy(['scope' => [CoreScope::SHIPPING, Scope::ALL]]) as $zone) {
            $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
                'name' => 'Free',
                'code' => 'FREE-' . $zone->getCode(),
                'enabled' => true,
                'zone' => $zone,
                'calculator' => [
                    'type' => DefaultCalculators::FLAT_RATE,
                    'configuration' => $this->getConfigurationByChannels([$this->sharedStorage->get('channel')]),
                ],
            ]));
        }
    }

    /**
     * @Given /^the store ships everywhere for free for (all channels)$/
     */
    public function theStoreShipsEverywhereForFreeForAllChannels(array $channels)
    {
        foreach ($this->zoneRepository->findBy(['scope' => [CoreScope::SHIPPING, Scope::ALL]]) as $zone) {
            $configuration = $this->getConfigurationByChannels($channels);
            $shippingMethod = $this->shippingMethodExampleFactory->create([
                'name' => 'Free',
                'enabled' => true,
                'zone' => $zone,
                'calculator' => [
                    'type' => DefaultCalculators::FLAT_RATE,
                    'configuration' => $configuration,
                ],
                'channels' => $channels,
            ]);

            $this->saveShippingMethod($shippingMethod);
        }
    }

    /**
     * @Given the store (also )allows shipping with :name
     */
    public function theStoreAllowsShippingMethodWithName($name)
    {
        $this->saveShippingMethod($this->shippingMethodExampleFactory->create(['name' => $name, 'enabled' => true]));
    }

    /**
     * @Given the store (also )allows shipping with :name identified by :code
     */
    public function theStoreAllowsShippingMethodWithNameAndCode($name, $code)
    {
        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $name,
            'zone' => $this->getShippingZone(),
            'enabled' => true,
            'code' => $code,
        ]));
    }

    /**
     * @Given the store (also )allows shipping with :name at position :position
     */
    public function theStoreAllowsShippingMethodWithNameAndPosition($name, $position)
    {
        $shippingMethod = $this->shippingMethodExampleFactory->create([
            'name' => $name,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
        ]);

        $shippingMethod->setPosition($position);

        $this->saveShippingMethod($shippingMethod);
    }

    /**
     * @Given /^(this shipping method) is named "([^"]+)" in the "([^"]+)" locale$/
     */
    public function thisShippingMethodIsNamedInLocale(ShippingMethodInterface $shippingMethod, $name, $locale)
    {
        /** @var ShippingMethodTranslationInterface $translation */
        $translation = $this->shippingMethodTranslationFactory->createNew();
        $translation->setLocale($locale);
        $translation->setName($name);

        $shippingMethod->addTranslation($translation);

        $this->shippingMethodManager->flush();
    }

    /**
     * @Given the store allows shipping with :firstName and :secondName
     */
    public function theStoreAllowsShippingWithAnd(...$names)
    {
        foreach ($names as $name) {
            $this->theStoreAllowsShippingMethodWithName($name);
        }
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee within the ("[^"]+" zone)$/
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee for the (rest of the world)$/
     */
    public function storeHasShippingMethodWithFeeAndZone($shippingMethodName, $fee, ZoneInterface $zone)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $zone,
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function storeHasShippingMethodWithFee($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per shipment for ("[^"]+" channel) and ("[^"]+") for ("[^"]+" channel)$/
     */
    public function storeHasShippingMethodWithFeePerShipmentForChannels(
        $shippingMethodName,
        $firstFee,
        ChannelInterface $firstChannel,
        $secondFee,
        ChannelInterface $secondChannel
    ) {
        $configuration[$firstChannel->getCode()] = ['amount' => $firstFee];
        $configuration[$secondChannel->getCode()] = ['amount' => $secondFee];

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$firstChannel, $secondChannel],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per unit for ("[^"]+" channel) and ("[^"]+") for ("[^"]+" channel)$/
     */
    public function storeHasShippingMethodWithFeePerUnitForChannels(
        $shippingMethodName,
        $firstFee,
        ChannelInterface $firstChannel,
        $secondFee,
        ChannelInterface $secondChannel
    ) {
        $configuration = [];
        $configuration[$firstChannel->getCode()] = ['amount' => $firstFee];
        $configuration[$secondChannel->getCode()] = ['amount' => $secondFee];

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::PER_UNIT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$firstChannel, $secondChannel],
        ]));
    }

    /**
     * @Given /^the store has disabled "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function storeHasDisabledShippingMethodWithFee($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => false,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has an archival "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function theStoreHasArchivalShippingMethodWithFee($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
            'archived_at' => new \DateTime(),
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per unit$/
     */
    public function theStoreHasShippingMethodWithFeePerUnit($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::PER_UNIT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [$this->sharedStorage->get('channel')],
        ]));
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee not assigned to any channel$/
     */
    public function storeHasShippingMethodWithFeeNotAssignedToAnyChannel($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->saveShippingMethod($this->shippingMethodExampleFactory->create([
            'name' => $shippingMethodName,
            'enabled' => true,
            'zone' => $this->getShippingZone(),
            'calculator' => [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ],
            'channels' => [],
        ]));
    }

    /**
     * @Given /^(shipping method "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function shippingMethodBelongsToTaxCategory(ShippingMethodInterface $shippingMethod, TaxCategoryInterface $taxCategory)
    {
        $shippingMethod->setTaxCategory($taxCategory);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given the shipping method :shippingMethod is enabled
     */
    public function theShippingMethodIsEnabled(ShippingMethodInterface $shippingMethod)
    {
        $shippingMethod->enable();
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given the shipping method :shippingMethod is disabled
     */
    public function theShippingMethodIsDisabled(ShippingMethodInterface $shippingMethod)
    {
        $shippingMethod->disable();
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^(this shipping method) requires at least one unit matches to ("([^"]+)" shipping category)$/
     */
    public function thisShippingMethodRequiresAtLeastOneUnitMatchToShippingCategory(
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory
    ) {
        $shippingMethod->setCategory($shippingCategory);
        $shippingMethod->setCategoryRequirement(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^(this shipping method) requires that all units match to ("([^"]+)" shipping category)$/
     */
    public function thisShippingMethodRequiresThatAllUnitsMatchToShippingCategory(
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory
    ) {
        $shippingMethod->setCategory($shippingCategory);
        $shippingMethod->setCategoryRequirement(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^(this shipping method) requires that no units match to ("([^"]+)" shipping category)$/
     */
    public function thisShippingMethodRequiresThatNoUnitsMatchToShippingCategory(
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory
    ) {
        $shippingMethod->setCategory($shippingCategory);
        $shippingMethod->setCategoryRequirement(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE);
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^the (shipping method "[^"]+") is archival$/
     */
    public function theShippingMethodIsArchival(ShippingMethodInterface $shippingMethod)
    {
        $shippingMethod->setArchivedAt(new \DateTime());
        $this->shippingMethodManager->flush();
    }

    /**
     * @Given /^the shipping fee for ("[^"]+" shipping method) has been changed to ("[^"]+")$/
     */
    public function theShippingFeeForShippingMethodHasBeenChangedTo(ShippingMethodInterface $shippingMethod, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $shippingMethod->setConfiguration($configuration);

        $this->shippingMethodManager->flush();
    }

    /**
     * @param array $channels
     * @param int $amount
     *
     * @return array
     */
    private function getConfigurationByChannels(array $channels, $amount = 0)
    {
        $configuration = [];

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $configuration[$channel->getCode()] = ['amount' => $amount];
        }

        return $configuration;
    }

    /**
     * @param ShippingMethodInterface $shippingMethod
     */
    private function saveShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        $this->shippingMethodRepository->add($shippingMethod);
        $this->sharedStorage->set('shipping_method', $shippingMethod);
    }

    /**
     * @return ZoneInterface
     */
    private function getShippingZone()
    {
        if ($this->sharedStorage->has('shipping_zone')) {
            return  $this->sharedStorage->get('shipping_zone');
        }

        return $this->sharedStorage->get('zone');
    }
}
