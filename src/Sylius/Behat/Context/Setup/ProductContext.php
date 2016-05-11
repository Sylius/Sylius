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
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
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
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @var AttributeFactoryInterface
     */
    private $productAttributeFactory;

    /**
     * @var FactoryInterface
     */
    private $productVariantFactory;

    /**
     * @var FactoryInterface
     */
    private $attributeValueFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductRepositoryInterface $productRepository
     * @param FactoryInterface $productFactory
     * @param AttributeFactoryInterface $productAttributeFactory
     * @param FactoryInterface $productVariantFactory
     * @param FactoryInterface $attributeValueFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        FactoryInterface $productFactory,
        AttributeFactoryInterface $productAttributeFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $attributeValueFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productAttributeFactory = $productAttributeFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->attributeValueFactory = $attributeValueFactory;
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
     * @Given /^(this product) has ([^"]+) attribute "([^"]+)" with value "([^"]+)"$/
     */
    public function thisProductHasAttributeWithValue(ProductInterface $product, $productAttributeType, $productAttributeName, $value)
    {
        $this->createProductAttribute($productAttributeType,$productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has percent attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function thisProductHasPercentAttributeWithValue(ProductInterface $product, $productAttributeName, $value)
    {
        $this->createProductAttribute('percent',$productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value/100);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has ([^"]+) attribute "([^"]+)" set to "([^"]+)"$/
     */
    public function thisProductHasCheckboxAttributeWithValue(ProductInterface $product, $productAttributeType, $productAttributeName, $value)
    {
        $this->createProductAttribute($productAttributeType, $productAttributeName);
        $booleanValue = ('Yes' === $value);
        $attributeValue = $this->createProductAttributeValue($booleanValue);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has ([^"]+) attribute "([^"]+)" with date "([^"]+)"$/
     */
    public function thisProductHasDateTimeAttributeWithDate(ProductInterface $product, $productAttributeType, $productAttributeName, $date)
    {
        $this->createProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue(new \DateTime($date));

        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
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

        $this->objectManager->persist($productAttribute);
        $this->sharedStorage->set('product_attribute', $productAttribute);
    }

    /**
     * @param string $value
     *
     * @return AttributeValueInterface
     */
    private function createProductAttributeValue($value)
    {
        /** @var AttributeValueInterface $attributeValue */
        $attributeValue = $this->attributeValueFactory->createNew();
        $attributeValue->setAttribute($this->sharedStorage->get('product_attribute'));
        $attributeValue->setValue($value);

        $this->objectManager->persist($attributeValue);

        return $attributeValue;
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
