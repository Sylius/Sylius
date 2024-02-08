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
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Repository\ProductOptionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductOptionContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ProductOptionRepositoryInterface $productOptionRepository,
        private FactoryInterface $productOptionFactory,
        private FactoryInterface $productOptionValueFactory,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @Given the store has (also) a product option :name
     * @Given the store has a product option :name with a code :code
     */
    public function theStoreHasAProductOptionWithACode(string $name, ?string $code = null): void
    {
        $productOption = $this->createProductOption($name, $code);

        $this->sharedStorage->set('product_option', $productOption);
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
        $productOptionValueCode,
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
        $productOption->setPosition((null === $position) ? null : (int) $position);

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
