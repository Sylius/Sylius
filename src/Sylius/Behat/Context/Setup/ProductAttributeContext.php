<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductAttributeContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var AttributeFactoryInterface
     */
    private $productAttributeFactory;

    /**
     * @var FactoryInterface
     */
    private $productAttributeValueFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $productAttributeRepository
     * @param AttributeFactoryInterface $productAttributeFactory
     * @param FactoryInterface $productAttributeValueFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productAttributeRepository,
        AttributeFactoryInterface $productAttributeFactory,
        FactoryInterface $productAttributeValueFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeFactory = $productAttributeFactory;
        $this->productAttributeValueFactory = $productAttributeValueFactory;
        $this->objectManager = $objectManager;

        $this->faker = \Faker\Factory::create();
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
     * @Given the store has( also) a :type product attribute :name at position :position
     */
    public function theStoreHasAProductAttributeWithPosition($type, $name, $position)
    {
        $productAttribute = $this->createProductAttribute($type, $name);
        $productAttribute->setPosition((int) $position);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given /^the store has(?:| also)(?:| a| an) (text|textarea|integer|percent) product attribute "([^"]+)"$/
     */
    public function theStoreHasAProductAttribute(string $type, string $name): void
    {
        $productAttribute = $this->createProductAttribute($type, $name);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given /^(this product attribute) has(?:| also) a value "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisProductAttributeHasAValueInLocale(
        ProductAttributeInterface $productAttribute,
        string $value,
        string $localeCode
    ): void {
        $choices = [
            $this->faker->uuid => [
                $localeCode => $value,
            ],
        ];

        $configuration = $productAttribute->getConfiguration();
        $configuration['choices'] = array_merge($configuration['choices'], $choices);
        $productAttribute->setConfiguration($configuration);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given /^(this product attribute) has(?:| also) a value "([^"]+)" in ("[^"]+" locale) and "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisProductAttributeHasAValueInLocaleAndInLocale(
        ProductAttributeInterface $productAttribute,
        string $firstValue,
        string $firstLocaleCode,
        string $secondValue,
        string $secondLocaleCode
    ): void {
        $choices = [
            $this->faker->uuid => [
                $firstLocaleCode => $firstValue,
                $secondLocaleCode => $secondValue,
            ],
        ];

        $configuration = $productAttribute->getConfiguration();
        $configuration['choices'] = array_merge($configuration['choices'], $choices);
        $productAttribute->setConfiguration($configuration);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given the store has a select product attribute :name
     */
    public function theStoreHasASelectProductAttribute(string $name): void
    {
        $this->theStoreHasASelectProductAttributeWithValue($name);
    }

    /**
     * @Given the store has a select product attribute :name with value :value
     * @Given the store has a select product attribute :name with values :firstValue and :secondValue
     */
    public function theStoreHasASelectProductAttributeWithValue(string $name, string ...$values): void
    {
        $choices = [];
        foreach ($values as $value) {
            $choices[$this->faker->uuid] = ['en_US' => $value];
        }

        $productAttribute = $this->createProductAttribute(SelectAttributeType::TYPE, $name);
        $productAttribute->setConfiguration([
            'multiple' => true,
            'choices' => $choices,
            'min' => null,
            'max' => null,
        ]);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given /^(this product attribute) has set min value as (\d+) and max value as (\d+)$/
     */
    public function thisAttributeHasSetMinValueAsAndMaxValueAs(ProductAttributeInterface $attribute, $min, $max)
    {
        $attribute->setConfiguration(['min' => $min, 'max' => $max]);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has(?:| also)(?:| a) select attribute "([^"]+)" with value "([^"]+)"$/
     * @Given /^(this product) has(?:| also)(?:| a) select attribute "([^"]+)" with values "([^"]+)" and "([^"]+)"$/
     */
    public function thisProductHasSelectAttributeWithValues(
        ProductInterface $product,
        string $productAttributeName,
        string ...$productAttributeValues
    ): void {
        $this->createSelectProductAttributeValue($product, $productAttributeName, $productAttributeValues);
    }

    /**
     * @Given /^(this product) has(?:| also)(?:| a) select attribute "([^"]+)" with value "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisProductHasSelectAttributeWithValueInLocale(
        ProductInterface $product,
        string $productAttributeName,
        string $productAttributeValue,
        string $localeCode
    ): void {
        $this->createSelectProductAttributeValue($product, $productAttributeName, [$productAttributeValue], $localeCode);
    }

    /**
     * @Given /^(this product) has (text|textarea) attribute "([^"]+)" with value "([^"]+)"$/
     * @Given /^(this product) has (text|textarea) attribute "([^"]+)" with value "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisProductHasAttributeWithValue(
        ProductInterface $product,
        string $productAttributeType,
        string $productAttributeName,
        string $value,
        string $language = 'en_US'
    ): void {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value, $attribute, $language);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has percent attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function thisProductHasPercentAttributeWithValue(ProductInterface $product, $productAttributeName, $value)
    {
        $attribute = $this->provideProductAttribute('percent', $productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value / 100, $attribute);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has ([^"]+) attribute "([^"]+)" set to "([^"]+)"$/
     */
    public function thisProductHasCheckboxAttributeWithValue(
        ProductInterface $product,
        $productAttributeType,
        $productAttributeName,
        $value
    ) {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $booleanValue = ('Yes' === $value);
        $attributeValue = $this->createProductAttributeValue($booleanValue, $attribute);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has percent attribute "([^"]+)" at position (\d+)$/
     */
    public function thisProductHasPercentAttributeWithValueAtPosition(
        ProductInterface $product,
        $productAttributeName,
        $position
    ) {
        $attribute = $this->provideProductAttribute('percent', $productAttributeName);
        $attribute->setPosition((int) $position);
        $attributeValue = $this->createProductAttributeValue(random_int(1, 100) / 100, $attribute);

        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has ([^"]+) attribute "([^"]+)" with date "([^"]+)"$/
     */
    public function thisProductHasDateTimeAttributeWithDate(
        ProductInterface $product,
        $productAttributeType,
        $productAttributeName,
        $date
    ) {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue(new \DateTime($date), $attribute);

        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
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
        $productAttribute = $this->productAttributeFactory->createTyped($type);

        $code = $code ?: StringInflector::nameToCode($name);

        $productAttribute->setCode($code);
        $productAttribute->setName($name);

        return $productAttribute;
    }

    /**
     * @param string $type
     * @param string $name
     * @param string|null $code
     *
     * @return ProductAttributeInterface
     */
    private function provideProductAttribute($type, $name, $code = null)
    {
        $code = $code ?: StringInflector::nameToCode($name);

        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $this->productAttributeRepository->findOneBy(['code' => $code]);
        if (null !== $productAttribute) {
            return $productAttribute;
        }

        $productAttribute = $this->createProductAttribute($type, $name, $code);
        $this->saveProductAttribute($productAttribute);

        return $productAttribute;
    }

    /**
     * @param mixed $value
     * @param ProductAttributeInterface $attribute
     * @param string $localeCode
     *
     * @return ProductAttributeValueInterface
     */
    private function createProductAttributeValue(
        $value,
        ProductAttributeInterface $attribute,
        string $localeCode = 'en_US'
    ): ProductAttributeValueInterface {
        /** @var ProductAttributeValueInterface $attributeValue */
        $attributeValue = $this->productAttributeValueFactory->createNew();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setValue($value);
        $attributeValue->setLocaleCode($localeCode);

        $this->objectManager->persist($attributeValue);

        return $attributeValue;
    }

    /**
     * @param ProductAttributeInterface $productAttribute
     */
    private function saveProductAttribute(ProductAttributeInterface $productAttribute)
    {
        $this->productAttributeRepository->add($productAttribute);
        $this->sharedStorage->set('product_attribute', $productAttribute);
    }

    /**
     * @param ProductInterface $product
     * @param string $productAttributeName
     * @param array $values
     * @param string $localeCode
     */
    private function createSelectProductAttributeValue(
        ProductInterface $product,
        string $productAttributeName,
        array $values,
        string $localeCode = 'en_US'
    ): void {
        $attribute = $this->provideProductAttribute(SelectAttributeType::TYPE, $productAttributeName);

        $choices = $attribute->getConfiguration()['choices'];
        $choiceKeys = [];
        foreach ($values as $value) {
            foreach ($choices as $choiceKey => $choiceValues) {
                $key = array_search($value, $choiceValues);
                if ($localeCode === $key) {
                    $choiceKeys[] = $choiceKey;
                }
            }
        }

        $attributeValue = $this->createProductAttributeValue($choiceKeys, $attribute, $localeCode);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }
}
