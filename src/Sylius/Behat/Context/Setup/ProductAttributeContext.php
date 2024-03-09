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
use Faker\Factory;
use Faker\Generator;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
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
    private Generator $faker;

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $productAttributeRepository,
        private AttributeFactoryInterface $productAttributeFactory,
        private FactoryInterface $productAttributeValueFactory,
        private ObjectManager $objectManager,
    ) {
        $this->faker = Factory::create();
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
     * @Given /^the store has(?:| also)(?:| a| an) (text|textarea|integer|percent|float) product attribute "([^"]+)"$/
     */
    public function theStoreHasAProductAttribute(string $type, string $name): void
    {
        $productAttribute = $this->createProductAttribute($type, $name);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given /^the store has(?:| also)(?:| a| an) non-translatable (text|textarea|integer|percent|float) product attribute "([^"]+)"$/
     */
    public function theStoreHasANonTranslatableProductAttribute(string $type, string $name): void
    {
        $productAttribute = $this->createProductAttribute($type, $name, null, false);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given /^(this product attribute) has(?:| also) a value "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisProductAttributeHasAValueInLocale(
        ProductAttributeInterface $productAttribute,
        string $value,
        string $localeCode,
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
        string $secondLocaleCode,
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
     * @Given the store has a non-translatable select product attribute :name with value :value
     */
    public function theStoreHasANonTranslatableSelectProductAttributeWithValue(string $name, string $value): void
    {
        $choices[$this->faker->uuid] = ['en_US' => $value];

        $productAttribute = $this->createProductAttribute(SelectAttributeType::TYPE, $name);
        $productAttribute->setConfiguration([
            'multiple' => true,
            'choices' => $choices,
            'min' => null,
            'max' => null,
        ]);
        $productAttribute->setTranslatable(false);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given the store has a non-translatable date product attribute :name with format :format
     */
    public function theStoreHasANonTranslatableDateProductAttributeWithFormat(string $name, string $format): void
    {
        $productAttribute = $this->createProductAttribute(DateAttributeType::TYPE, $name);
        $productAttribute->setConfiguration([
            'format' => $format,
        ]);
        $productAttribute->setTranslatable(false);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given the store has a non-translatable datetime product attribute :name with format :format
     */
    public function theStoreHasANonTranslatableDatetimeProductAttributeWithFormat(string $name, string $format): void
    {
        $productAttribute = $this->createProductAttribute(DatetimeAttributeType::TYPE, $name);
        $productAttribute->setConfiguration([
            'format' => $format,
        ]);
        $productAttribute->setTranslatable(false);

        $this->saveProductAttribute($productAttribute);
    }

    /**
     * @Given /^(this product attribute)'s "([^"]+)" value is labeled "([^"]+)" in the ("[^"]+" locale)$/
     */
    public function thisProductAttributeValueIsLabeledInTheLocale(
        ProductAttributeInterface $attribute,
        string $value,
        string $label,
        string $localeCode,
    ): void {
        $uuid = $this->getSelectAttributeValueUuidByChoiceValue($attribute, $value);

        $configuration = $attribute->getConfiguration();
        $choices[$uuid] = $configuration['choices'][$uuid] + [$localeCode => $label];
        $configuration['choices'] = $choices;

        $attribute->setConfiguration($configuration);
    }

    private function getSelectAttributeValueUuidByChoiceValue(
        ProductAttributeInterface $attribute,
        string $value,
    ): string {
        $choices = $attribute->getConfiguration()['choices'] ?? [];

        foreach ($choices as $uuid => $choice) {
            foreach ($choice as $choiceValue) {
                if ($value === $choiceValue) {
                    return $uuid;
                }
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'Value "%s" not found in attribute %s',
            $value,
            $attribute->getName(),
        ));
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
        string ...$productAttributeValues,
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
        string $localeCode,
    ): void {
        $this->createSelectProductAttributeValue($product, $productAttributeName, [$productAttributeValue], $localeCode);
    }

    /**
     * @Given /^(this product) has a (text|textarea) attribute "([^"]+)" with value "([^"]+)"$/
     * @Given /^(this product) has a (text|textarea) attribute "([^"]+)" with value "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisProductHasAttributeWithValue(
        ProductInterface $product,
        string $productAttributeType,
        string $productAttributeName,
        string $value,
        string $language = 'en_US',
    ): void {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value, $attribute, $language);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has non-translatable (text|textarea) attribute "([^"]+)" with value "([^"]+)"$/
     */
    public function thisProductHasNonTranslatableTextAttributeWithValue(
        ProductInterface $product,
        string $productAttributeType,
        string $productAttributeName,
        string $value,
        string $language = 'en_US',
    ): void {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value, $attribute, $language, false);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has a percent attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function thisProductHasPercentAttributeWithValue(ProductInterface $product, $productAttributeName, $value)
    {
        $attribute = $this->provideProductAttribute('percent', $productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value / 100, $attribute);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has non-translatable percent attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function thisProductHasNonTranslatablePercentAttributeWithValue(ProductInterface $product, string $productAttributeName, int $value): void
    {
        $attribute = $this->provideProductAttribute('percent', $productAttributeName);
        $attributeValue = $this->createProductAttributeValue($value / 100, $attribute, null, false);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has a "([^"]+)" attribute "([^"]+)" set to "([^"]+)"$/
     */
    public function thisProductHasCheckboxAttributeWithValue(
        ProductInterface $product,
        $productAttributeType,
        $productAttributeName,
        $value,
    ) {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $booleanValue = ('Yes' === $value);
        $attributeValue = $this->createProductAttributeValue($booleanValue, $attribute);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has non-translatable "([^"]+)" attribute "([^"]+)" set to "([^"]+)"$/
     */
    public function thisProductHasNonTranslatableCheckboxAttributeWithValue(
        ProductInterface $product,
        string $productAttributeType,
        string $productAttributeName,
        $value,
    ) {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $booleanValue = ('Yes' === $value);
        $attributeValue = $this->createProductAttributeValue($booleanValue, $attribute, 'en_US', false);
        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has percent attribute "([^"]+)" at position (\d+)$/
     */
    public function thisProductHasPercentAttributeWithValueAtPosition(
        ProductInterface $product,
        $productAttributeName,
        $position,
    ) {
        $attribute = $this->provideProductAttribute('percent', $productAttributeName);
        $attribute->setPosition((int) $position);
        $attributeValue = $this->createProductAttributeValue(random_int(1, 100) / 100, $attribute);

        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has a ([^"]+) attribute "([^"]+)" with date "([^"]+)"$/
     */
    public function thisProductHasDateTimeAttributeWithDate(
        ProductInterface $product,
        $productAttributeType,
        $productAttributeName,
        $date,
    ) {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue(new \DateTime($date), $attribute);

        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has non-translatable ([^"]+) attribute "([^"]+)" with date "([^"]+)"$/
     */
    public function thisProductHasNonTranslatableDateTimeAttributeWithDate(
        ProductInterface $product,
        string $productAttributeType,
        string $productAttributeName,
        $date,
    ) {
        $attribute = $this->provideProductAttribute($productAttributeType, $productAttributeName);
        $attributeValue = $this->createProductAttributeValue(new \DateTime($date), $attribute, 'en_US', false);

        $product->addAttribute($attributeValue);

        $this->objectManager->flush();
    }

    /**
     * @When /^(this product attribute)'s value changed from "([^"]+)" to "([^"]+)"$/
     */
    public function thisAttributeValueChangedFromTo(
        ProductAttributeInterface $attribute,
        string $from,
        string $to,
    ): void {
        $configuration = $attribute->getConfiguration();
        $choices = $configuration['choices'] ?? [];

        foreach ($choices as $uuid => $choice) {
            foreach ($choice as $localeCode => $item) {
                if ($item === $from) {
                    $choices[$uuid][$localeCode] = $to;

                    break 2;
                }
            }
        }

        $configuration['choices'] = $choices;
        $attribute->setConfiguration($configuration);

        $this->objectManager->flush();
    }

    /**
     * @When /^(this product attribute)'s value "([^"]+)" has been removed$/
     */
    public function thisAttributeValueHasBeenRemoved(
        ProductAttributeInterface $attribute,
        string $value,
    ): void {
        $configuration = $attribute->getConfiguration();
        $choices = $configuration['choices'] ?? [];

        foreach ($choices as $uuid => $choice) {
            foreach ($choice as $item) {
                if ($value === $item) {
                    unset($choices[$uuid]);

                    break 2;
                }
            }
        }

        $configuration['choices'] = $choices;
        $attribute->setConfiguration($configuration);

        $this->objectManager->flush();
    }

    private function createProductAttribute(
        string $type,
        string $name,
        ?string $code = null,
        bool $translatable = true,
    ): ProductAttributeInterface {
        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $this->productAttributeFactory->createTyped($type);

        $code = $code ?: StringInflector::nameToCode($name);

        $productAttribute->setCode($code);
        $productAttribute->setTranslatable($translatable);
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

    private function createProductAttributeValue(
        $value,
        ProductAttributeInterface $attribute,
        ?string $localeCode = 'en_US',
        bool $translatable = true,
    ): ProductAttributeValueInterface {
        $attribute->setTranslatable($translatable);
        $this->objectManager->persist($attribute);

        /** @var ProductAttributeValueInterface $attributeValue */
        $attributeValue = $this->productAttributeValueFactory->createNew();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setValue($value);
        $attributeValue->setLocaleCode($localeCode);

        $this->objectManager->persist($attributeValue);

        return $attributeValue;
    }

    private function saveProductAttribute(ProductAttributeInterface $productAttribute)
    {
        $this->productAttributeRepository->add($productAttribute);
        $this->sharedStorage->set('product_attribute', $productAttribute);
    }

    private function createSelectProductAttributeValue(
        ProductInterface $product,
        string $productAttributeName,
        array $values,
        string $localeCode = 'en_US',
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
