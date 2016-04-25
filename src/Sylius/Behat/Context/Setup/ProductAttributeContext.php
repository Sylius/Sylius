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
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Attribute\Repository\AttributeRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductAttributeContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var AttributeFactoryInterface
     */
    private $productAttributeFactory;

    /**
     * @var AttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param AttributeRepositoryInterface $productAttributeRepository
     * @param AttributeFactoryInterface $productAttributeFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        AttributeRepositoryInterface $productAttributeRepository,
        AttributeFactoryInterface $productAttributeFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeFactory = $productAttributeFactory;
    }

    /**
     * @Given the store has a :type product attribute :name with code :code
     */
    public function theStoreHasAProductAttributeWithCode($type, $name, $code)
    {
        $this->createProductAttribute($type, $name, $code);
    }

    /**
     * @Given the store has a :type product attribute :name
     */
    public function theStoreHasATextProductAttribute($type, $name)
    {
        $this->createProductAttribute($type, $name);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $code
     */
    private function createProductAttribute($type, $name, $code = 'PA112')
    {
        $productAttribute = $this->productAttributeFactory->createTyped($type);
        $productAttribute->setCode($code);
        $productAttribute->setName($name);

        $this->productAttributeRepository->add($productAttribute);
        $this->sharedStorage->set('product_attribute', $productAttribute);
    }
}
