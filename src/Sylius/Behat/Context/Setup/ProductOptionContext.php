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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Repository\ProductOptionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductOptionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductOptionRepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @var FactoryInterface
     */
    private $productOptionFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionValueFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductOptionRepositoryInterface $productOptionRepository
     * @param FactoryInterface $productOptionFactory
     * @param FactoryInterface $productOptionValueFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductOptionRepositoryInterface $productOptionRepository,
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productOptionRepository = $productOptionRepository;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store has a product option :name
     * @Given the store has a product option :name with a code :code
     */
    public function theStoreHasAProductOptionWithACode($name, $code = null)
    {
        $this->createProductOption($name, $code);
    }

    /**
     * @Given /^the store has(?:| also) a product option "([^"]+)" at position ([^"]+)$/
     */
    public function theStoreHasAProductOptionAtPosition($name, $position)
    {
        $this->createProductOption($name, null, $position);
    }

    /**
     * @Given /^(this product option) has(?:| also) the "([^"]+)" option value with code "([^"]+)"$/
     */
    public function thisProductOptionHasTheOptionValueWithCode(
        ProductOptionInterface $productOption,
        $productOptionValueName,
        $productOptionValueCode
    ) {
        $productOptionValue = $this->createProductOptionValue($productOptionValueName, $productOptionValueCode);
        $productOption->addValue($productOptionValue);

        $this->objectManager->flush();
    }

    /**
     * @param string $name
     * @param string|null $code
     * @param string|null $position
     *
     * @return ProductOptionInterface
     */
    private function createProductOption($name, $code = null, $position = null)
    {
        /** @var ProductOptionInterface $productOption */
        $productOption = $this->productOptionFactory->createNew();
        $productOption->setName($name);
        $productOption->setCode($code ?: StringInflector::nameToCode($name));
        $productOption->setPosition($position);

        $this->sharedStorage->set('product_option', $productOption);
        $this->productOptionRepository->add($productOption);

        return $productOption;
    }

    /**
     * @param string $value
     * @param string $code
     *
     * @return ProductOptionValueInterface
     */
    private function createProductOptionValue($value, $code)
    {
        /** @var ProductOptionValueInterface $productOptionValue */
        $productOptionValue = $this->productOptionValueFactory->createNew();
        $productOptionValue->setValue($value);
        $productOptionValue->setCode($code);

        return $productOptionValue;
    }
}
