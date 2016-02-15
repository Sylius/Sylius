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
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param RepositoryInterface $shippingMethodRepository
     * @param RepositoryInterface $zoneRepository
     * @param FactoryInterface $shippingMethodFactory
     * @param ObjectManager $shippingMethodManager
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        RepositoryInterface $shippingMethodRepository,
        RepositoryInterface $zoneRepository,
        FactoryInterface $shippingMethodFactory,
        ObjectManager $shippingMethodManager,
        SharedStorageInterface $sharedStorage
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->zoneRepository = $zoneRepository;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingMethodManager = $shippingMethodManager;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform :shippingMethodName shipping method
     * @Transform shipping method :shippingMethodName
     */
    public function getShippingMethodByName($shippingMethodName)
    {
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['name' => $shippingMethodName]);
        if (null === $shippingMethod) {
            throw new \Exception('Shipping method with name "'.$shippingMethodName.'" does not exist');
        }

        return $shippingMethod;
    }

    /**
     * @Given the store ships everything for free
     * @Given the store ships everything for free within :zone zone
     * @Given /^the store ships everything for free for (the rest of the world)$/
     */
    public function storeShipsEverythingForFree(ZoneInterface $zone = null)
    {
        $this->createShippingMethod('Free', $zone);
    }

    /**
     * @Given /^the store ships everything for free to all available locations$/
     */
    public function theStoreShipsEverythingForFreeToAllAvailableLocations()
    {
        foreach ($this->zoneRepository->findAll() as $zone) {
            $this->createShippingMethod('Free', $zone);
        }
    }

    /**
     * @Given /^the store has "([^"]*)" shipping method with "(?:€|£|\$)([^"]*)" fee$/
     * @Given /^the store has "([^"]*)" shipping method with "(?:€|£|\$)([^"]*)" fee within ("([^"]*)" zone)$/
     * @Given /^the store has "([^"]*)" shipping method with "(?:€|£|\$)([^"]*)" fee for (the rest of the world)$/
     */
    public function storeHasShippingMethodWithFee($shippingMethodName, $fee, ZoneInterface $zone = null)
    {
        $this->createShippingMethod($shippingMethodName, $zone, 'en', ['amount' => $this->getFeeFromString($fee)]);
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
            $zone = $this->sharedStorage->getCurrentResource('zone');
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

    /**
     * @param string $shippingMethodFee
     *
     * @return string
     */
    private function getFeeFromString($shippingMethodFee)
    {
        return ((int) $shippingMethodFee) * 100;
    }
}
