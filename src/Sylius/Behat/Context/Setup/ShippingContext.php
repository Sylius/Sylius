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
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
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
     * @var RepositoryInterface
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
     * @var ObjectManager
     */
    private $shippingMethodManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $shippingMethodRepository
     * @param RepositoryInterface $zoneRepository
     * @param FactoryInterface $shippingMethodFactory
     * @param ObjectManager $shippingMethodManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $shippingMethodRepository,
        RepositoryInterface $zoneRepository,
        FactoryInterface $shippingMethodFactory,
        ObjectManager $shippingMethodManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->zoneRepository = $zoneRepository;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingMethodManager = $shippingMethodManager;
    }

    /**
     * @Given the store ships everything for free within :zone zone
     * @Given /^the store ships everything for free for (the rest of the world)$/
     */
    public function storeShipsEverythingForFree(ZoneInterface $zone = null)
    {
        $this->createShippingMethod('Free', $zone);
    }

    /**
     * @Given /^the store ships everywhere for free$/
     */
    public function theStoreShipsEverywhereForFree()
    {
        foreach ($this->zoneRepository->findAll() as $zone) {
            $this->createShippingMethod('Free', $zone);
        }
    }

    /**
     * @Given /^the store has "([^"]*)" shipping method with ("[^"]+") fee$/
     * @Given /^the store has "([^"]*)" shipping method with ("[^"]+") fee within ("([^"]*)" zone)$/
     * @Given /^the store has "([^"]*)" shipping method with ("[^"]+") fee for (the rest of the world)$/
     */
    public function storeHasShippingMethodWithFee($shippingMethodName, $fee, ZoneInterface $zone = null)
    {
        $this->createShippingMethod($shippingMethodName, $zone, 'en', ['amount' => $fee]);
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
     * @param string $name
     * @param string $locale
     * @param array $configuration
     * @param string $calculator
     * @param ZoneInterface|null $zone
     */
    private function createShippingMethod(
        $name,
        $zone = null,
        $locale = 'en',
        $configuration = ['amount' => 0],
        $calculator = DefaultCalculators::FLAT_RATE
    ) {
        if (null === $zone) {
            $zone = $this->sharedStorage->get('zone');
        }

        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($this->generateCodeFromNameAndZone($name, $zone->getCode()));
        $shippingMethod->setName($name);
        $shippingMethod->setCurrentLocale($locale);
        $shippingMethod->setConfiguration($configuration);
        $shippingMethod->setCalculator($calculator);
        $shippingMethod->setZone($zone);

        $this->shippingMethodRepository->add($shippingMethod);
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
