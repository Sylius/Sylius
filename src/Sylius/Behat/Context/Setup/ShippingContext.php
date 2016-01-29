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
class ShippingContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param RepositoryInterface $shippingMethodRepository
     * @param FactoryInterface $shippingMethodFactory
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        RepositoryInterface $shippingMethodRepository,
        FactoryInterface $shippingMethodFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^"([^"]+)" shipping method/
     * @Transform /^shipping method "([^"]+)"$/
     */
    public function castShippingMethodNameToShippingMethod($shippingMethodName)
    {
        if (null === $shippingMethod = $this->shippingMethodRepository->findOneBy(['name' => $shippingMethodName])) {
            throw new \Exception('Shipping method with name "'.$shippingMethodName.'" does not exist');
        }

        return $shippingMethod;
    }

    /**
     * @Given store ships everything for free
     * @Given /^store ships everything for free within ("([^"]*)" zone)$/
     */
    public function storeShipsEverythingForFree(ZoneInterface $zone = null)
    {
        $this->createShippingMethod('Free', $zone);
    }

    /**
     * @Given /^store has "([^"]*)" shipping method with "(€|£|\$)([^"]*)" fee$/
     * @Given /^store has "([^"]*)" shipping method with "(€|£|\$)([^"]*)" fee within ("([^"]*)" zone)$/
     * @Given /^store has "([^"]*)" shipping method with "(€|£|\$)([^"]*)" fee for (the rest of the world)$/
     */
    public function storeHasShippingMethodWithFee($shippingMethodName, $currency, $fee, ZoneInterface $zone = null)
    {
        $this->createShippingMethod($shippingMethodName, 'FR', ['amount' => $this->getFeeFromString($fee)]);
    }

    /**
     * @Given /^(shipping method "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function productBelongsToTaxCategory(ShippingMethodInterface $shippingMethod, TaxCategoryInterface $taxCategory)
    {
        $shippingMethod->setTaxCategory($taxCategory);
        $this->shippingMethodRepository->add($shippingMethod);
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
        $locale = 'FR',
        $configuration = array('amount' => 0),
        $calculator = DefaultCalculators::PER_ITEM_RATE
    ) {
        if (null === $zone) {
            $zone = $this->sharedStorage->getCurrentResource('zone');
        }

        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($this->getCodeFromName($name));
        $shippingMethod->setName($name);
        $shippingMethod->setCurrentLocale($locale);
        $shippingMethod->setConfiguration($configuration);
        $shippingMethod->setCalculator($calculator);
        $shippingMethod->setZone($zone);

        $this->shippingMethodRepository->add($shippingMethod);
    }

    /**
     * @param string $shippingMethodName
     *
     * @return string
     */
    private function getCodeFromName($shippingMethodName)
    {
        return str_replace(array(' ', '-'), '_', strtolower($shippingMethodName));
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
