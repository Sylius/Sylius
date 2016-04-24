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
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Variation\Repository\OptionRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var OptionRepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionValueFactory;

    /**
     * @var FactoryInterface
     */
    private $productVariantFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductRepositoryInterface $productRepository
     * @param OptionRepositoryInterface $productOptionRepository
     * @param FactoryInterface $productFactory
     * @param FactoryInterface $productOptionFactory
     * @param FactoryInterface $productOptionValueFactory
     * @param FactoryInterface $productVariantFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        OptionRepositoryInterface $productOptionRepository,
        FactoryInterface $productFactory,
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        FactoryInterface $productVariantFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productFactory = $productFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given /^the store has a product "([^"]+)"$/
     * @Given /^the store has a product "([^"]+)" priced at ("[^"]+")$/
     */
    public function storeHasAProductPricedAt($productName, $price = 0)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $product->setName($productName);
        $product->setPrice($price);
        $product->setDescription('Awesome '.$productName);

        $channel = $this->sharedStorage->get('channel');
        $product->addChannel($channel);

        $this->productRepository->add($product);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Given /^the (product "[^"]+") has "([^"]+)" variant priced at ("[^"]+")$/
     * @Given /^(this product) has "([^"]+)" variant priced at ("[^"]+")$/
     */
    public function theProductHasVariantPricedAt(ProductInterface $product, $productVariantName, $price)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        $variant->setPresentation($productVariantName);
        $variant->setPrice($price);
        $variant->setProduct($product);
        $product->addVariant($variant);

        $this->objectManager->flush();

        $this->sharedStorage->set('variant', $variant);
    }

    /**
     * @Given /^there is product "([^"]+)" available in ((?:this|that|"[^"]+") channel)$/
     */
    public function thereIsProductAvailableInGivenChannel($productName, ChannelInterface $channel)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $product->setName($productName);
        $product->setPrice(0);
        $product->setDescription('Awesome ' . $productName);
        $product->addChannel($channel);

        $this->productRepository->add($product);
    }

    /**
     * @Given /^([^"]+) belongs to ("[^"]+" tax category)$/
     */
    public function productBelongsToTaxCategory(ProductInterface $product, TaxCategoryInterface $taxCategory)
    {
        $product->getMasterVariant()->setTaxCategory($taxCategory);
        $this->objectManager->flush();
    }

    /**
     * @Given /^(it) comes in the following variations:$/
     */
    public function itComesInTheFollowingVariations(ProductInterface $product, TableNode $table)
    {
        foreach ($table->getHash() as $variantHash) {
            /** @var ProductVariantInterface $variant */
            $variant = $this->productVariantFactory->createNew();

            $variant->setPresentation($variantHash['name']);
            $variant->setPrice($this->getPriceFromString(str_replace(['$', '€', '£'], '', $variantHash['price'])));
            $variant->setProduct($product);
            $product->addVariant($variant);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^("[^"]+" variant of product "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function productVariantBelongsToTaxCategory(
        ProductVariantInterface $productVariant,
        TaxCategoryInterface $taxCategory
    ) {
        $productVariant->setTaxCategory($taxCategory);
        $this->objectManager->flush($productVariant);
    }

    /**
     * @Given the store has a product option :productOptionName with a code :productOptionCode
     */
    public function theStoreHasAProductOptionWithACode($productOptionName, $productOptionCode)
    {
        $productOption = $this->productOptionFactory->createNew();
        $productOption->setCode($productOptionCode);
        $productOption->setName($productOptionName);

        $this->sharedStorage->set('product_option', $productOption);
        $this->productOptionRepository->add($productOption);
    }

    /**
     * @Given /^(this product option) has(?:| also) the "([^"]+)" option value with code "([^"]+)"$/
     */
    public function thisProductOptionHasTheOptionValueWithCode(
        OptionInterface $productOption,
        $productOptionValueName,
        $productOptionValueCode
    ) {
        $productOptionValue = $this->createProductOptionValue($productOptionValueName, $productOptionValueCode);
        $productOption->addValue($productOptionValue);

        $this->objectManager->flush();
    }

    /**
     * @param string $value
     * @param string $code
     *
     * @return OptionValueInterface
     */
    private function createProductOptionValue($value, $code)
    {
        $productOptionValue = $this->productOptionValueFactory->createNew();
        $productOptionValue->setValue($value);
        $productOptionValue->setCode($code);

        return $productOptionValue;
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round(($price * 100), 2);
    }
}
