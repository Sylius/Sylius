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
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Attribute\Repository\AttributeRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Product\Model\ProductAttributeInterface;

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
        $productAttribute = $this->createProductAttribute($type, $name, $code);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given the store( also) has a :type product attribute :name at position :position
     */
    public function theStoreHasAProductAttributeWithPosition($type, $name, $position)
    {
        $productAttribute = $this->createProductAttribute($type, $name);
        $productAttribute->setPosition($position);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given the store( also) has a :type product attribute :name
     */
    public function theStoreHasATextProductAttribute($type, $name)
    {
        $productAttribute = $this->createProductAttribute($type, $name);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string|null $code
     *
     * @return ProductAttributeInterface
     */
    private function createProductAttribute($type, $name, $code = null)
    {
        $productAttribute = $this->productAttributeFactory->createWithType($type);

        if (null === $code) {
            $code = StringInflector::nameToUppercaseCode($name);
        }

        $productAttribute->setCode($code);
        $productAttribute->setName($name);

        return $productAttribute;
    }

    /**
     * @param ProductAttributeInterface $productAttribute
     */
    private function saveProductAttribute(ProductAttributeInterface $productAttribute)
    {
        $this->productAttributeRepository->add($productAttribute);
        $this->sharedStorage->set('product_attribute', $productAttribute);
    }
}
