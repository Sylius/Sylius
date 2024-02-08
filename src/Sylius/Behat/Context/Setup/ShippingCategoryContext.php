<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

final class ShippingCategoryContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FactoryInterface $shippingCategoryFactory,
        private RepositoryInterface $shippingCategoryRepository,
    ) {
    }

    /**
     * @Given the store has :firstShippingCategoryName shipping category
     * @Given the store has :firstShippingCategoryName and :secondShippingCategoryName shipping category
     */
    public function theStoreHasAndShippingCategory($firstShippingCategoryName, $secondShippingCategoryName = null)
    {
        $this->createShippingCategory($firstShippingCategoryName);
        (null === $secondShippingCategoryName) ?: $this->createShippingCategory($secondShippingCategoryName);
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
        $shippingCategory = $this->shippingCategoryFactory->createNew();
        $shippingCategory->setName($shippingCategoryName);
        $shippingCategory->setCode($shippingCategoryCode);

        if (null === $shippingCategoryCode) {
            $shippingCategory->setCode(StringInflector::nameToCode($shippingCategoryName));
        }

        $this->shippingCategoryRepository->add($shippingCategory);
        $this->sharedStorage->set('shipping_category', $shippingCategory);
    }
}
