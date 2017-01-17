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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ShippingCategoryContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $shippingCategoryFactory;

    /**
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $shippingCategoryFactory
     * @param RepositoryInterface $shippingCategoryRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $shippingCategoryFactory,
        RepositoryInterface $shippingCategoryRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shippingCategoryFactory = $shippingCategoryFactory;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
    }

    /**
     * @Given the store has :firstShippingCategoryName shipping category
     * @Given the store has :firstShippingCategoryName and :secondShippingCategoryName shipping category
     */
    public function theStoreHasAndShippingCategory($firstShippingCategoryName, $secondShippingCategoryName = null)
    {
        $this->createShippingCategory($firstShippingCategoryName);
        (null === $secondShippingCategoryName)? : $this->createShippingCategory($secondShippingCategoryName);
    }

    /**
     * @Given the store has :shippingCategoryName shipping category identified by :shippingCategoryCode
     */
    public function theStoreHasShippingCategoryIdentifiedBy($shippingCategoryName, $shippingCategoryCode)
    {
        $this->createShippingCategory($shippingCategoryName, $shippingCategoryCode);
    }

    /**
     * @param string $shippingCategoryName
     * @param string $shippingCategoryCode
     */
    private function createShippingCategory($shippingCategoryName, $shippingCategoryCode = null)
    {
        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory =  $this->shippingCategoryFactory->createNew();
        $shippingCategory->setName($shippingCategoryName);
        $shippingCategory->setCode($shippingCategoryCode);

        if (null === $shippingCategoryCode) {
            $shippingCategory->setCode(StringInflector::nameToCode($shippingCategoryName));
        }

        $this->shippingCategoryRepository->add($shippingCategory);
        $this->sharedStorage->set('shipping_category', $shippingCategory);
    }
}
