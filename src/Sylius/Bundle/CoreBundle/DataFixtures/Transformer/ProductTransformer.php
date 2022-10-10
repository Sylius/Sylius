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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Faker\Generator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateProductAttributeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateTaxonTrait;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductTransformer implements ProductTransformerInterface
{
    use FindOrCreateProductAttributeTrait;
    use FindOrCreateTaxonTrait;
    use TransformNameToCodeAttributeTrait;
    use TransformNameToSlugAttributeTrait;
    use TransformTaxCategoryAttributeTrait;
    use TransformChannelsAttributeTrait;
    use TransformTaxaAttributeTrait;
    use TransformProductOptionsAttributeTrait;

    private Generator $faker;

    public function __construct(
        private SlugGeneratorInterface $slugGenerator,
        private EventDispatcherInterface $eventDispatcher,
        private RepositoryInterface $localeRepository,
        private FactoryInterface $productAttributeValueFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes = $this->transformNameToSlugAttribute($attributes);
        $attributes = $this->transformTaxCategoryAttribute($this->eventDispatcher, $attributes);
        $attributes = $this->transformChannelsAttribute($this->eventDispatcher, $attributes);
        $attributes = $this->transformMainTaxonAttribute($attributes);
        $attributes = $this->transformTaxaAttribute($this->eventDispatcher, $attributes);
        $attributes = $this->transformProductOptionsAttribute($this->eventDispatcher, $attributes);

        return $this->transformProductAttributeValues($attributes);
    }

    private function transformMainTaxonAttribute(array $attributes): array
    {
        if (\is_string($attributes['main_taxon'])) {
            $attributes['main_taxon'] = $this->findOrCreateTaxon($this->eventDispatcher, ['code' => $attributes['main_taxon']]);
        }

        return $attributes;
    }

    private function transformProductAttributeValues(array $attributes): array
    {
        $productAttributesValues = [];
        foreach ($attributes['product_attributes'] as $code => $value) {
            $productAttribute = $this->findOrCreateProductAttribute($this->eventDispatcher, ['code' => $code]);

            if (!$productAttribute->isTranslatable()) {
                $productAttributesValues[] = $this->configureProductAttributeValue($productAttribute->object(), null, $value);

                continue;
            }

            foreach ($this->getLocales() as $localeCode) {
                $productAttributesValues[] = $this->configureProductAttributeValue($productAttribute->object(), $localeCode, $value);
            }
        }

        $attributes['product_attributes'] = $productAttributesValues;

        return $attributes;
    }

    private function configureProductAttributeValue(ProductAttributeInterface $productAttribute, ?string $localeCode, $value): ProductAttributeValueInterface
    {
        /** @var ProductAttributeValueInterface $productAttributeValue */
        $productAttributeValue = $this->productAttributeValueFactory->createNew();
        $productAttributeValue->setAttribute($productAttribute);

        if ($value !== null && in_array($productAttribute->getStorageType(), [ProductAttributeValueInterface::STORAGE_DATE, ProductAttributeValueInterface::STORAGE_DATETIME], true)) {
            $value = new \DateTime($value);
        }

        $productAttributeValue->setValue($value ?? $this->getRandomValueForProductAttribute($productAttribute));
        $productAttributeValue->setLocaleCode($localeCode);

        return $productAttributeValue;
    }

    /**
     * @throws \BadMethodCallException
     */
    private function getRandomValueForProductAttribute(ProductAttributeInterface $productAttribute)
    {
        switch ($productAttribute->getStorageType()) {
            case ProductAttributeValueInterface::STORAGE_BOOLEAN:
                return $this->faker->boolean;
            case ProductAttributeValueInterface::STORAGE_INTEGER:
                return $this->faker->numberBetween(0, 10000);
            case ProductAttributeValueInterface::STORAGE_FLOAT:
                return $this->faker->randomFloat(4, 0, 10000);
            case ProductAttributeValueInterface::STORAGE_TEXT:
                return $this->faker->sentence;
            case ProductAttributeValueInterface::STORAGE_DATE:
            case ProductAttributeValueInterface::STORAGE_DATETIME:
                return $this->faker->dateTimeThisCentury;
            case ProductAttributeValueInterface::STORAGE_JSON:
                if ($productAttribute->getType() === SelectAttributeType::TYPE) {
                    if ($productAttribute->getConfiguration()['multiple']) {
                        return $this->faker->randomElements(
                            array_keys($productAttribute->getConfiguration()['choices']),
                            $this->faker->numberBetween(1, count($productAttribute->getConfiguration()['choices'])),
                        );
                    }

                    return [$this->faker->randomKey($productAttribute->getConfiguration()['choices'])];
                }
            // no break
            default:
                throw new \BadMethodCallException();
        }
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface $locale */
        foreach ($this->localeRepository->findAll() as $locale) {
            yield $locale->getCode();
        }
    }
}
