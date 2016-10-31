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
use Behat\Mink\Element\NodeElement;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @var FactoryInterface
     */
    private $productTranslationFactory;

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
     * @var FactoryInterface
     */
    private $productImageFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ProductVariantResolverInterface
     */
    private $defaultVariantResolver;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactoryInterface $productFactory
     * @param FactoryInterface $productTranslationFactory
     * @param AttributeFactoryInterface $productAttributeFactory
     * @param FactoryInterface $productVariantFactory
     * @param FactoryInterface $attributeValueFactory
     * @param FactoryInterface $productOptionFactory
     * @param FactoryInterface $productOptionValueFactory
     * @param FactoryInterface $productImageFactory
     * @param ObjectManager $objectManager
     * @param ProductVariantResolverInterface $defaultVariantResolver
     * @param ImageUploaderInterface $imageUploader
     * @param array $minkParameters
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        ProductFactoryInterface $productFactory,
        FactoryInterface $productTranslationFactory,
        AttributeFactoryInterface $productAttributeFactory,
        FactoryInterface $attributeValueFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        FactoryInterface $productImageFactory,
        ObjectManager $objectManager,
        ProductVariantResolverInterface $defaultVariantResolver,
        ImageUploaderInterface $imageUploader,
        array $minkParameters
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productTranslationFactory = $productTranslationFactory;
        $this->productAttributeFactory = $productAttributeFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->productImageFactory = $productImageFactory;
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
        $this->defaultVariantResolver = $defaultVariantResolver;
        $this->minkParameters = $minkParameters;
    }

    /**
     * @Given the store has a product :productName
     * @Given the store has a :productName product
     * @Given /^the store(?:| also) has a product "([^"]+)" priced at ("[^"]+")$/
     */
    public function storeHasAProductPricedAt($productName, $price = 0)
    {
        $product = $this->createProduct($productName, $price);

        $product->setDescription('Awesome '.$productName);

        if ($this->sharedStorage->has('channel')) {
            $product->addChannel($this->sharedStorage->get('channel'));
        }

        $this->saveProduct($product);
    }

    /**
     * @Given the store( also) has a product :productName with code :code
     * @Given the store( also) has a product :productName with code :code, created at :date
     */
    public function storeHasProductWithCode($productName, $code, $date = null)
    {
        $product = $this->createProduct($productName, 0, $date);

        $product->setCode($code);

        if ($this->sharedStorage->has('channel')) {
            $product->addChannel($this->sharedStorage->get('channel'));
        }

        $this->saveProduct($product);
    }

    /**
     * @Given /^(this product) is named "([^"]+)" (in the "([^"]+)" locale)$/
     */
    public function thisProductIsNamedIn(ProductInterface $product, $name, $locale)
    {
        /** @var ProductTranslationInterface $translation */
        $translation = $this->productTranslationFactory->createNew();
        $translation->setLocale($locale);
        $translation->setName($name);

        $product->addTranslation($translation);

        $this->objectManager->flush();
    }

    /**
     * @Given the store has a :productName configurable product
     */
    public function storeHasAConfigurableProduct($productName)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $product->setName($productName);
        $product->setCode(StringInflector::nameToUppercaseCode($productName));
        $product->setDescription('Awesome '.$productName);

        if ($this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
            $product->addChannel($channel);
        }

        $this->saveProduct($product);
    }

    /**
     * @Given the store has( also) :firstProductName and :secondProductName products
     */
    public function theStoreHasAProductAnd($firstProductName, $secondProductName)
    {
        $this->saveProduct($this->createProduct($firstProductName));
        $this->saveProduct($this->createProduct($secondProductName));
    }

    /**
     * @Given /^the (product "[^"]+") has(?:| a) "([^"]+)" variant priced at ("[^"]+")$/
     * @Given /^(this product) has "([^"]+)" variant priced at ("[^"]+")$/
     */
    public function theProductHasVariantPricedAt(ProductInterface $product, $productVariantName, $price)
    {
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_CHOICE);

        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        $variant->setName($productVariantName);
        $variant->setCode(StringInflector::nameToUppercaseCode($productVariantName));
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
        /** @var ProductVariantInterface $variant */
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
            $variant->setCode(StringInflector::nameToUppercaseCode($variantHash['name']));
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
     * @Given /^(this product) has option "([^"]+)" with values "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function thisProductHasOptionWithValues(ProductInterface $product, $optionName, ...$values)
    {
        /** @var ProductOptionInterface $option */
        $option = $this->productOptionFactory->createNew();

        $option->setName($optionName);
        $option->setCode(StringInflector::nameToUppercaseCode($optionName));

        $this->sharedStorage->set(sprintf('%s_option', $optionName), $option);

        foreach ($values as $key => $value) {
            $optionValue = $this->addProductOption($option, $value, StringInflector::nameToUppercaseCode($value));
            $this->sharedStorage->set(sprintf('%s_option_%s_value', $value, strtolower($optionName)), $optionValue);
        }

        $product->addOption($option);
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->objectManager->persist($option);
        $this->objectManager->flush();
    }

    /**
     * @Given /^there (?:is|are) (\d+) unit(?:|s) of (product "([^"]+)") available in the inventory$/
     * @When product :product quantity is changed to :quantity
     */
    public function thereIsQuantityOfProducts($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setOnHand($quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the (product "([^"]+)") is out of stock$/
     */
    public function theProductIsOutOfStock(ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setTracked(true);
        $productVariant->setOnHand(0);

        $this->objectManager->flush();
    }

    /**
     * @When other customer has bought :quantity :product products by this time
     */
    public function otherCustomerHasBoughtProductsByThisTime($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setOnHand($productVariant->getOnHand() - $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) is tracked by the inventory$/
     * @Given /^(?:|the )("[^"]+" product) is(?:| also) tracked by the inventory$/
     */
    public function thisProductIsTrackedByTheInventory(ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setTracked(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) is available in "([^"]+)" ([^"]+) priced at ("[^"]+")$/
     */
    public function thisProductIsAvailableInSize(ProductInterface $product, $optionValueName, $optionName, $price)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        $optionValue = $this->sharedStorage->get(sprintf('%s_option_%s_value', $optionValueName, $optionName));

        $variant->addOptionValue($optionValue);
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
    public function thisProductHasThisProductOption(ProductInterface $product, ProductOptionInterface $option)
    {
        $product->addOption($option);

        $this->objectManager->flush();
    }

    /**
     * @Given /^there are ([^"]+) units of ("[^"]+" variant of product "[^"]+") available in the inventory$/
     */
    public function thereAreItemsOfProductInVariantAvailableInTheInventory($quantity, ProductVariantInterface $productVariant)
    {
        $productVariant->setTracked(true);
        $productVariant->setOnHand($quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the ("[^"]+" product variant) is tracked by the inventory$/
     */
    public function theProductVariantIsTrackedByTheInventory(ProductVariantInterface $productVariant)
    {
        $productVariant->setTracked(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product)'s price is ("[^"]+")$/
     * @Given /^the (product "[^"]+") changed its price to ("[^"]+")$/
     */
    public function theProductChangedItsPriceTo(ProductInterface $product, $price)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setPrice($price);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has(?:| also) an image "([^"]+)" with a code "([^"]+)"$/
     * @Given /^the ("[^"]+" product) has(?:| also) an image "([^"]+)" with a code "([^"]+)"$/
     */
    public function thisProductHasAnImageWithACode(ProductInterface $product, $imagePath, $imageCode)
    {
        $filesPath = $this->getParameter('files_path');

        /** @var ImageInterface $productImage */
        $productImage = $this->productImageFactory->createNew();
        $productImage->setFile(new UploadedFile($filesPath.$imagePath, basename($imagePath)));
        $productImage->setCode($imageCode);
        $this->imageUploader->upload($productImage);

        $product->addImage($productImage);

        $this->objectManager->flush($product);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $code
     *
     * @return ProductAttributeInterface
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
     * @return ProductAttributeValueInterface
     */
    private function createProductAttributeValue($value, ProductAttributeInterface $attribute)
    {
        /** @var ProductAttributeValueInterface $attributeValue */
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
    private function createProduct($productName, $price = 0, $date = null)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createWithVariant();

        $product->setName($productName);
        $product->setCode(StringInflector::nameToUppercaseCode($productName));
        $product->setCreatedAt(new \DateTime($date));

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setPrice($price);
        $productVariant->setCode($product->getCode());

        return $product;
    }

    /**
     * @param ProductOptionInterface $option
     * @param string $value
     * @param string $code
     *
     * @return ProductOptionValueInterface
     */
    private function addProductOption(ProductOptionInterface $option, $value, $code)
    {
        /** @var ProductOptionValueInterface $optionValue */
        $optionValue = $this->productOptionValueFactory->createNew();

        $optionValue->setValue($value);
        $optionValue->setCode($code);
        $optionValue->setOption($option);

        $option->addValue($optionValue);

        return $optionValue;
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
     * @param string $name
     *
     * @return NodeElement
     */
    private function getParameter($name)
    {
        return isset($this->minkParameters[$name]) ? $this->minkParameters[$name] : null;
    }
}
