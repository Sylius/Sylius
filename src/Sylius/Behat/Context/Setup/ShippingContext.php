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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;

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
     * @Given store ships everything for free
     */
    public function storeShipsEverythingForFree()
    {
        $this->createShippingMethod('Free');
    }

    /**
     * @Given /^store has "([^"]*)" shipping method with "(?:€|£|\$)([^"]*)" fee$/
     */
    public function storeHasShippingMethodWithFee($shippingMethodName, $fee)
    {
        $this->createShippingMethod($shippingMethodName, 'en', ['amount' => $this->getFeeFromString($fee)]);
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
        $locale = 'en',
        $configuration = ['amount' => 0],
        $calculator = DefaultCalculators::FLAT_RATE,
        $zone = null
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
        return str_replace(' ', '_', strtolower($shippingMethodName));
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
