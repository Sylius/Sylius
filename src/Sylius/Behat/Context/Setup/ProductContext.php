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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Variation\Resolver\VariantResolverInterface;

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
     * @var ProductFactoryInterface
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
     * @var VariantResolverInterface
     */
    private $defaultVariantResolver;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactoryInterface $productFactory
     * @param AttributeFactoryInterface $productAttributeFactory
     * @param FactoryInterface $productVariantFactory
     * @param FactoryInterface $attributeValueFactory
     * @param FactoryInterface $productOptionFactory
     * @param FactoryInterface $productOptionValueFactory
     * @param ObjectManager $objectManager
     * @param VariantResolverInterface $defaultVariantResolver
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        ProductFactoryInterface $productFactory,
        AttributeFactoryInterface $productAttributeFactory,
        FactoryInterface $attributeValueFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        ObjectManager $objectManager,
        VariantResolverInterface $defaultVariantResolver
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productAttributeFactory = $productAttributeFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->objectManager = $objectManager;
        $this->defaultVariantResolver = $defaultVariantResolver;
    }

    /**
     * @Given the store has a product :productName
     * @Given the store has a :productName product
     * @Given /^the store has a product "([^"]+)" priced at ("[^"]+")$/
     */
    public function storeHasAProductPricedAt($productName, $price = 0)
    {
        $product = $this->createProduct($productName, $price);

        $product->setDescription('Awesome '.$productName);

        if ($this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
            $product->addChannel($channel);
        }

        $this->saveProduct($product);
    }

    /**
     * @Given the store has a :productName configurable product
     */
    public function storeHasAConfigurableProduct($productName)
    {
        $product = $this->productFactory->createNew();

        $product->setName($productName);
        $product->setCode($this->convertToCode($productName));
        $product->setDescription('Awesome '.$productName);

        $this->saveProduct($product);
    }

    /**
     * @Given the store has :firstProductName and :secondProductName products
     */
    public function theStoreHasAProductAnd($firstProductName, $secondProductName)
    {
        $this->saveProduct($this->createProduct($firstProductName));
        $this->saveProduct($this->createProduct($secondProductName));
    }

    /**
     * @Given /^the (product "[^"]+") has "([^"]+)" variant priced at ("[^"]+")$/
     * @Given /^(this product) has "([^"]+)" variant priced at ("[^"]+")$/
     */
    public function theProductHasVariantPricedAt(ProductInterface $product, $productVariantName, $price)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        $variant->setName($productVariantName);
        $variant->setCode($this->convertToCode($productVariantName));
        $variant->setPrice($price);
        $variant->setProduct($product);
        $product->addVariant($variant);

        $this->objectManager->flush();

        $this->sharedStorage->set('variant', $variant);
    }

    /**
     * @Given /^there is product "([^"]+)" available in ((?:this|that|"[^"]+") channel)$/
     * @Given /^the store has a product "([^"]+)" available in ("([^"]+)" channel)$/
     */
    public function thereIsProductAvailableInGivenChannel($productName, ChannelInterface $channel)
    {
        $product = $this->createProduct($productName);

        $product->setDescription('Awesome ' . $productName);
        $product->addChannel($channel);

        $this->saveProduct($product);
    }

    /**
     * @Given /^([^"]+) belongs to ("[^"]+" tax category)$/
     */
    public function productBelongsToTaxCategory(ProductInterface $product, TaxCategoryInterface $taxCategory)
    {
        $variant = $this->defaultVariantResolver->getVariant($product);
        $variant->setTaxCategory($taxCategory);
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

            $variant->setName($variantHash['name']);
            $variant->setCode($this->convertToCode($variantHash['name']));
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
        $attribute = $this->createProductAttribute($productAttributeType,$productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value, $attribute);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has percent attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function thisProductHasPercentAttributeWithValue(ProductInterface $product, $productAttributeName, $value)
    {
        $attribute = $this->createProductAttribute('percent',$productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value/100, $attribute);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has ([^"]+) attribute "([^"]+)" set to "([^"]+)"$/
     */
    public function thisProductHasCheckboxAttributeWithValue(ProductInterface $product, $productAttributeType, $productAttributeName, $value)
    {
        $attribute = $this->createProductAttribute($productAttributeType, $productAttributeName);
        $booleanValue = ('Yes' === $value);
        $attributeValue = $this->createProductAttributeValue($booleanValue, $attribute);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has ([^"]+) attribute "([^"]+)" with date "([^"]+)"$/
     */
    public function thisProductHasDateTimeAttributeWithDate(ProductInterface $product, $productAttributeType, $productAttributeName, $date)
    {
        $attribute = $this->createProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue(new \DateTime($date), $attribute);

        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has option "([^"]+)" with values "([^"]+)" and "([^"]+)"$/
     */
    public function thisProductHasOptionWithValues(ProductInterface $product, $optionName, $firstValue, $secondValue)
    {
        /** @var OptionInterface $variant */
        $option = $this->productOptionFactory->createNew();

        $option->setName($optionName);
        $option->setCode('PO1');

        /** @var OptionValueInterface $optionValue */
        $firstOptionValue = $this->productOptionValueFactory->createNew();

        $firstOptionValue->setValue($firstValue);
        $firstOptionValue->setCode('POV1');
        $firstOptionValue->setOption($option);

        /** @var OptionValueInterface $optionValue */
        $secondOptionValue = $this->productOptionValueFactory->createNew();

        $secondOptionValue->setValue($secondValue);
        $secondOptionValue->setCode('POV2');
        $secondOptionValue->setOption($option);

        $option->addValue($firstOptionValue);
        $option->addValue($secondOptionValue);

        $product->addOption($option);
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->sharedStorage->set(sprintf('%s_option',$optionName), $option);
        $this->sharedStorage->set(sprintf('%s_option_value',$firstValue), $firstOptionValue);
        $this->sharedStorage->set(sprintf('%s_option_value',$secondValue), $secondOptionValue);

        $this->objectManager->persist($option);
        $this->objectManager->persist($firstOptionValue);
        $this->objectManager->persist($secondOptionValue);
        $this->objectManager->flush();
    }

    /**
     * @Given /^there (?:is|are) (\d+) item(?:s) of (product "([^"]+)") available in the inventory$/
     * @When product :product quantity is changed to :quantity
     */
    public function thereIsQuantityOfProducts($quantity, ProductInterface $product)
    {
        $this->setProductsQuantity($product, $quantity);
    }

    /**
     * @Given /^the (product "([^"]+)") is out of stock$/
     */
    public function theProductIsNotAvailable(ProductInterface $product)
    {
        $product->getFirstVariant()->setTracked(true);

        $this->setProductsQuantity($product, 0);
    }

    /**
     * @Given /^(this product) is available in "([^"]+)" size priced at ("[^"]+")$/
     */
    public function thisProductIsAvailableInSize(ProductInterface $product, $optionValueName, $price)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        $optionValue = $this->sharedStorage->get(sprintf('%s_option_value',$optionValueName));

        $variant->addOption($optionValue);
        $variant->setPrice($price);
        $variant->setCode(sprintf("%s_%s", $product->getCode(), $optionValueName));

        $product->addVariant($variant);
        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has (this product option)$/
     * @Given /^(this product) has a ("[^"]+" option)$/
     * @Given /^(this product) has an ("[^"]+" option)$/
     */
    public function thisProductHasThisProductOption(ProductInterface $product, OptionInterface $option)
    {
        $product->addOption($option);

        $this->objectManager->flush();
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $code
     *
     * @return AttributeInterface
     */
    private function createProductAttribute($type, $name, $code = 'PA112')
    {
        $productAttribute = $this->productAttributeFactory->createTyped($type);
        $productAttribute->setCode($code);
        $productAttribute->setName($name);

        $this->objectManager->persist($productAttribute);

        return $productAttribute;
    }

    /**
     * @param string $value
     *
     * @return AttributeValueInterface
     */
    private function createProductAttributeValue($value, AttributeInterface $attribute)
    {
        /** @var AttributeValueInterface $attributeValue */
        $attributeValue = $this->attributeValueFactory->createNew();
        $attributeValue->setAttribute($attribute);
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

    /**
     * @param string $productName
     * @param int $price
     *
     * @return ProductInterface
     */
    private function createProduct($productName, $price = 0)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createWithVariant();

        $product->setName($productName);
        $product->setCode($this->convertToCode($productName));

        $variant = $this->defaultVariantResolver->getVariant($product);
        $variant->setPrice($price);
        $variant->setCode($product->getCode());
        $variant->setTracked(true);

        return $product;
    }

    /**
     * @param ProductInterface $product
     * @param int $quantity
     */
    private function setProductsQuantity(ProductInterface $product, $quantity)
    {
        $product->getFirstVariant()->setOnHand($quantity);

        $this->saveProduct($product);
    }

    /**
     * @param ProductInterface $product
     */
    private function saveProduct(ProductInterface $product)
    {
        $this->productRepository->add($product);
        $this->sharedStorage->set('product', $product);
    }

    /**
     * @param string $productName
     *
     * @return string
     */
    private function convertToCode($productName)
    {
        return StringInflector::nameToUpercaseCode($productName);
    }
}
