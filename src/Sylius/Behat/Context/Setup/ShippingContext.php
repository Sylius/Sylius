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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
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
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

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
     * @param FactoryInterface $shippingMethodFactory
     * @param FactoryInterface $shippingMethodTranslationFactory
     * @param ObjectManager $shippingMethodManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        RepositoryInterface $zoneRepository,
        FactoryInterface $shippingMethodFactory,
        FactoryInterface $shippingMethodTranslationFactory,
        ObjectManager $shippingMethodManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->zoneRepository = $zoneRepository;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingMethodTranslationFactory = $shippingMethodTranslationFactory;
        $this->shippingMethodManager = $shippingMethodManager;
    }

    /**
     * @Given the store ships everything for free within the :zone zone
     * @Given /^the store ships everything for free for the (rest of the world)$/
     */
    public function storeShipsEverythingForFree(ZoneInterface $zone = null)
    {
        $this->createShippingMethod('Free', null, null, $zone);
    }

    /**
     * @Given the store ships everywhere for free
     */
    public function theStoreShipsEverywhereForFree()
    {
        foreach ($this->zoneRepository->findAll() as $zone) {
            $this->createShippingMethod('Free', null, null, $zone);
        }
    }

    /**
     * @Given /^the store ships everywhere for free for (all channels)$/
     */
    public function theStoreShipsEverywhereForFreeForAllChannels(array $channels)
    {
        foreach ($this->zoneRepository->findAll() as $zone) {
            $configuration = $this->getConfigurationByChannels($channels);
            $shipment = $this->createShippingMethod('Free', null, null, $zone, 'en', $configuration, DefaultCalculators::FLAT_RATE, true, false);

            foreach ($channels as $channel) {
                $shipment->addChannel($channel);
            }
        }
    }

    /**
     * @Given the store (also) allows shipping with :name
     * @Given the store (also) allows shipping with :name identified by :code
     * @Given the store (also) allows shipping with :name at position :position
     */
    public function theStoreAllowsShippingMethod($name, $code = null, $position = null)
    {
        $this->createShippingMethod($name, $code, $position);
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
    public function theStoreAllowsShippingWithAnd($firstName, $secondName)
    {
        $this->createShippingMethod($firstName);
        $this->createShippingMethod($secondName);
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee$/
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee within the ("[^"]+" zone)$/
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee for the (rest of the world)$/
     */
    public function storeHasShippingMethodWithFee($shippingMethodName, $fee, ZoneInterface $zone = null)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->createShippingMethod($shippingMethodName, null, null, $zone, 'en', $configuration);
    }

    /**
     * @Given /^the store has disabled "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function storeHasDisabledShippingMethodWithFee($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->createShippingMethod($shippingMethodName, null, null, null, 'en', $configuration, DefaultCalculators::FLAT_RATE, false);
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per unit$/
     */
    public function theStoreHasShippingMethodWithFeePerUnit($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->createShippingMethod($shippingMethodName, null, null, null, 'en', $configuration, DefaultCalculators::PER_UNIT_RATE);
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee not assigned to any channel$/
     */
    public function storeHasShippingMethodWithFeeNotAssignedToAnyChannel($shippingMethodName, $fee)
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->getConfigurationByChannels([$channel], $fee);

        $this->createShippingMethod($shippingMethodName, null, null, null, 'en', $configuration, DefaultCalculators::FLAT_RATE, false, false);
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
     * @param string $name
     * @param string|null $code
     * @param int|null $position
     * @param ZoneInterface|null $zone
     * @param string $locale
     * @param array $configuration
     * @param string $calculator
     * @param bool $enabled
     * @param bool $addForCurrentChannel
     *
     * @return ShippingMethodInterface
     */
    private function createShippingMethod(
        $name,
        $code = null,
        $position = null,
        ZoneInterface $zone = null,
        $locale = 'en',
        $configuration = null,
        $calculator = DefaultCalculators::FLAT_RATE,
        $enabled = true,
        $addForCurrentChannel = true
    ) {
        $channel = $this->sharedStorage->get('channel');

        if (null === $zone) {
            $zone = $this->sharedStorage->get('zone');
        }

        if (null === $code) {
            $code = $this->generateCodeFromNameAndZone($name, $zone->getCode());
        }

        if (null === $configuration) {
            $configuration = $this->getConfigurationByChannels([$channel]);
        }

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($code);
        $shippingMethod->setName($name);
        $shippingMethod->setPosition($position);
        $shippingMethod->setCurrentLocale($locale);
        $shippingMethod->setConfiguration($configuration);
        $shippingMethod->setCalculator($calculator);
        $shippingMethod->setZone($zone);
        $shippingMethod->setEnabled($enabled);

        if ($addForCurrentChannel && $this->sharedStorage->has('channel')) {
            $shippingMethod->addChannel($channel);
        }

        $this->shippingMethodRepository->add($shippingMethod);
        $this->sharedStorage->set('shipping_method', $shippingMethod);

        return $shippingMethod;
    }

    /**
     * @param string $shippingMethodName
     * @param string|null $zoneCode
     *
     * @return string
     */
    private function generateCodeFromNameAndZone($shippingMethodName, $zoneCode = null)
    {
        return StringInflector::nameToLowercaseCode($shippingMethodName).'_'.StringInflector::nameToLowercaseCode($zoneCode);
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
}
