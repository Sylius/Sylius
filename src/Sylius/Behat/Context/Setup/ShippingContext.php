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
     * @Given store ships everything for free
     */
    public function storeHasFreeShippingMethod()
    {
        $zone = $this->sharedStorage->getCurrentResource('zone');

        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode('SM1');
        $shippingMethod->setName('Free');
        $shippingMethod->setCurrentLocale('FR');
        $shippingMethod->setConfiguration(array('amount' => 0));
        $shippingMethod->setCalculator(DefaultCalculators::PER_ITEM_RATE);
        $shippingMethod->setZone($zone);

        $this->shippingMethodRepository->add($shippingMethod);
    }
}
