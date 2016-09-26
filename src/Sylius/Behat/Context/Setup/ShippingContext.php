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
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
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
     * @var ZoneRepositoryInterface
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
     * @param ZoneRepositoryInterface $zoneRepository
     * @param FactoryInterface $shippingMethodFactory
     * @param FactoryInterface $shippingMethodTranslationFactory
     * @param ObjectManager $shippingMethodManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneRepositoryInterface $zoneRepository,
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
     * @Given /^the store ships everything for free for (the rest of the world)$/
     */
    public function storeShipsEverythingForFree(ZoneInterface $zone = null)
    {
        $this->createShippingMethod('Free', null, $zone);
    }

    /**
     * @Given /^the store ships everywhere for free$/
     */
    public function theStoreShipsEverywhereForFree()
    {
        foreach ($this->zoneRepository->findAll() as $zone) {
            $this->createShippingMethod('Free', null, $zone);
        }
    }

    /**
     * @Given the store allows shipping with :name
     * @Given the store( also) allows shipping with :name identified by :code
     */
    public function theStoreAllowsShippingMethod($name, $code = null)
    {
        $this->createShippingMethod($name, $code);
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
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee for (the rest of the world)$/
     */
    public function storeHasShippingMethodWithFee($shippingMethodName, $fee, ZoneInterface $zone = null)
    {
        $this->createShippingMethod($shippingMethodName, null, $zone, 'en', ['amount' => $fee]);
    }

    /**
     * @Given /^the store has disabled "([^"]+)" shipping method with ("[^"]+") fee$/
     */
    public function storeHasDisabledShippingMethodWithFee($shippingMethodName, $fee)
    {
        $this->createShippingMethod($shippingMethodName, null, null, 'en', ['amount' => $fee], DefaultCalculators::FLAT_RATE, false);
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee per unit$/
     */
    public function theStoreHasShippingMethodWithFeePerUnit($shippingMethodName, $fee)
    {
        $this->createShippingMethod($shippingMethodName, null, null, 'en', ['amount' => $fee], DefaultCalculators::PER_UNIT_RATE);
    }

    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee on fist unit and ("[^"]+") on next (\d+)$/
     */
    public function theStoreHasShippingMethodWithFeeOnFistUnitAndOnNext($shippingMethodName, $fee, $perUnitFee, $limit)
    {
        $this->createShippingMethod(
            $shippingMethodName, 
            null, 
            null, 
            'en', 
            ['first_unit_cost' => $fee, 'additional_unit_cost' => $perUnitFee, 'additional_unit_limit' => $limit], 
            DefaultCalculators::FLEXIBLE_RATE
        );
    }
    
    /**
     * @Given /^the store has "([^"]+)" shipping method with ("[^"]+") fee not assigned to any channel$/
     */
    public function storeHasShippingMethodWithFeeNotAssignedToAnyChannel($shippingMethodName, $fee)
    {
        $this->createShippingMethod($shippingMethodName, null, null, 'en', ['amount' => $fee], DefaultCalculators::FLAT_RATE, false, false);
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
     * @param string $name
     * @param string|null $code
     * @param ZoneInterface|null $zone
     * @param string $locale
     * @param array $configuration
     * @param string $calculator
     * @param bool $enabled
     * @param bool $addForCurrentChannel
     */
    private function createShippingMethod(
        $name,
        $code = null,
        ZoneInterface $zone = null,
        $locale = 'en',
        $configuration = ['amount' => 0],
        $calculator = DefaultCalculators::FLAT_RATE,
        $enabled = true,
        $addForCurrentChannel = true
    ) {
        if (null === $zone) {
            $zone = $this->sharedStorage->get('zone');
        }

        if (null === $code) {
            $code = $this->generateCodeFromNameAndZone($name, $zone->getCode());
        }

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($code);
        $shippingMethod->setName($name);
        $shippingMethod->setCurrentLocale($locale);
        $shippingMethod->setConfiguration($configuration);
        $shippingMethod->setCalculator($calculator);
        $shippingMethod->setZone($zone);
        $shippingMethod->setEnabled($enabled);

        if ($addForCurrentChannel && $this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
            $channel->addShippingMethod($shippingMethod);
        }

        $this->shippingMethodRepository->add($shippingMethod);
        $this->sharedStorage->set('shipping_method', $shippingMethod);
    }

    /**
     * @param string $shippingMethodName
     * @param string|null $zoneCode
     *
     * @return string
     */
    private function generateCodeFromNameAndZone($shippingMethodName, $zoneCode = null)
    {
        return str_replace([' ', '-'], '_', strtolower($shippingMethodName)).'_'.strtolower($zoneCode);
    }
}
