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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class ProductExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private FactoryInterface $productFactory,
        private FactoryInterface $productVariantFactory,
        private FactoryInterface $channelPricingFactory,
        private ProductVariantGeneratorInterface $variantGenerator,
        private FactoryInterface $productAttributeValueFactory,
        private FactoryInterface $productImageFactory,
        private FactoryInterface $productTaxonFactory,
        private ImageUploaderInterface $imageUploader,
        private SlugGeneratorInterface $slugGenerator,
        private RepositoryInterface $taxonRepository,
        private RepositoryInterface $productAttributeRepository,
        private RepositoryInterface $productOptionRepository,
        private RepositoryInterface $channelRepository,
        private RepositoryInterface $localeRepository,
        private ?RepositoryInterface $taxCategoryRepository = null,
        private ?FileLocatorInterface $fileLocator = null,
    ) {
        if ($this->taxCategoryRepository === null) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.6',
                'Not passing a $taxCategoryRepository to %s constructor is deprecated and will be prohibited in Sylius 2.0.',
                self::class,
            );
        }

        if ($this->fileLocator === null) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.13',
                'Not passing a $fileLocator to %s constructor is deprecated and will be removed in Sylius 2.0.',
                self::class,
            );
        }

        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ProductInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setVariantSelectionMethod($options['variant_selection_method']);
        $product->setCode($options['code']);
        $product->setEnabled($options['enabled']);
        $product->setMainTaxon($options['main_taxon']);
        $product->setCreatedAt($this->faker->dateTimeBetween('-1 week', 'now'));

        $this->createTranslations($product, $options);
        $this->createRelations($product, $options);
        $this->createVariants($product, $options);
        $this->createImages($product, $options);
        $this->createProductTaxons($product, $options);

        return $product;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })

            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))

            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')

            ->setDefault('tracked', false)
            ->setAllowedTypes('tracked', 'bool')

            ->setDefault('slug', fn (Options $options): string => $this->slugGenerator->generate($options['name']))

            ->setDefault('short_description', fn (Options $options): string => $this->faker->paragraph)

            ->setDefault('description', function (Options $options): string {
                /** @var string $paragraphs */
                $paragraphs = $this->faker->paragraphs(3, true);

                return $paragraphs;
            })

            ->setDefault('main_taxon', LazyOption::randomOne($this->taxonRepository))
            ->setAllowedTypes('main_taxon', ['null', 'string', TaxonInterface::class])
            ->setNormalizer('main_taxon', LazyOption::findOneBy($this->taxonRepository, 'code'))

            ->setDefault('taxons', LazyOption::randomOnes($this->taxonRepository, 3))
            ->setAllowedTypes('taxons', 'array')
            ->setNormalizer('taxons', LazyOption::findBy($this->taxonRepository, 'code'))

            ->setDefault('channels', LazyOption::randomOnes($this->channelRepository, 3))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))

            ->setDefault('variant_selection_method', ProductInterface::VARIANT_SELECTION_MATCH)
            ->setAllowedTypes('variant_selection_method', 'string')
            ->setAllowedValues('variant_selection_method', [ProductInterface::VARIANT_SELECTION_MATCH, ProductInterface::VARIANT_SELECTION_CHOICE])

            ->setDefault('product_attributes', [])
            ->setAllowedTypes('product_attributes', 'array')
            ->setNormalizer('product_attributes', fn (Options $options, array $productAttributes): array => $this->setAttributeValues($productAttributes))

            ->setDefault('product_options', [])
            ->setAllowedTypes('product_options', 'array')
            ->setNormalizer('product_options', LazyOption::findBy($this->productOptionRepository, 'code'))

            ->setDefault('images', [])
            ->setAllowedTypes('images', 'array')

            ->setDefault('shipping_required', true)

            ->setDefault('tax_category', null)
            ->setAllowedTypes('tax_category', ['string', 'null', TaxCategoryInterface::class])
        ;

        if ($this->taxCategoryRepository !== null) {
            $resolver->setNormalizer('tax_category', LazyOption::findOneBy($this->taxCategoryRepository, 'code'));
        }
    }

    private function createTranslations(ProductInterface $product, array $options): void
    {
        foreach ($this->getLocales() as $localeCode) {
            $product->setCurrentLocale($localeCode);
            $product->setFallbackLocale($localeCode);

            $product->setName($options['name']);
            $product->setSlug($options['slug']);
            $product->setShortDescription($options['short_description']);
            $product->setDescription($options['description']);
        }
    }

    private function createRelations(ProductInterface $product, array $options): void
    {
        foreach ($options['channels'] as $channel) {
            $product->addChannel($channel);
        }

        foreach ($options['product_options'] as $option) {
            $product->addOption($option);
        }

        foreach ($options['product_attributes'] as $attribute) {
            $product->addAttribute($attribute);
        }
    }

    private function createVariants(ProductInterface $product, array $options): void
    {
        try {
            $this->variantGenerator->generate($product);
        } catch (\InvalidArgumentException) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $this->productVariantFactory->createNew();

            $product->addVariant($productVariant);
        }

        $i = 0;
        /** @var ProductVariantInterface $productVariant */
        foreach ($product->getVariants() as $productVariant) {
            $productVariant->setName($this->generateProductVariantName($productVariant));
            $productVariant->setCode(sprintf('%s-variant-%d', $options['code'], $i));
            $productVariant->setOnHand($this->faker->randomNumber(1));
            $productVariant->setShippingRequired($options['shipping_required']);
            if (isset($options['tax_category']) && $options['tax_category'] instanceof TaxCategoryInterface) {
                $productVariant->setTaxCategory($options['tax_category']);
            }
            $productVariant->setTracked($options['tracked']);

            /** @var ChannelInterface $channel */
            foreach ($this->channelRepository->findAll() as $channel) {
                $this->createChannelPricings($productVariant, $channel->getCode());
            }

            ++$i;
        }
    }

    private function createChannelPricings(ProductVariantInterface $productVariant, string $channelCode): void
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setChannelCode($channelCode);
        $channelPricing->setPrice($this->faker->numberBetween(100, 10000));

        $productVariant->addChannelPricing($channelPricing);
    }

    private function createImages(ProductInterface $product, array $options): void
    {
        foreach ($options['images'] as $image) {
            if (!array_key_exists('path', $image)) {
                trigger_deprecation(
                    'sylius/core-bundle',
                    '1.3',
                    'It is deprecated to pass indexed array as an image definition. Please use associative array with "path" and "type" keys instead.',
                );

                $imagePath = array_shift($image);
                $imageType = array_pop($image);
            } else {
                $imagePath = $image['path'];
                $imageType = $image['type'] ?? null;
            }

            $imagePath = $this->fileLocator === null ? $imagePath : $this->fileLocator->locate($imagePath);
            $uploadedImage = new UploadedFile($imagePath, basename($imagePath));

            /** @var ImageInterface $productImage */
            $productImage = $this->productImageFactory->createNew();
            $productImage->setFile($uploadedImage);
            $productImage->setType($imageType);

            $this->imageUploader->upload($productImage);

            $product->addImage($productImage);
        }
    }

    private function createProductTaxons(ProductInterface $product, array $options): void
    {
        foreach ($options['taxons'] as $taxon) {
            /** @var ProductTaxonInterface $productTaxon */
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setProduct($product);
            $productTaxon->setTaxon($taxon);

            $product->addProductTaxon($productTaxon);
        }
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    private function setAttributeValues(array $productAttributes): array
    {
        $productAttributesValues = [];
        foreach ($productAttributes as $code => $value) {
            /** @var ProductAttributeInterface|null $productAttribute */
            $productAttribute = $this->productAttributeRepository->findOneBy(['code' => $code]);

            Assert::notNull($productAttribute, sprintf('Can not find product attribute with code: "%s"', $code));

            if (!$productAttribute->isTranslatable()) {
                $productAttributesValues[] = $this->configureProductAttributeValue($productAttribute, null, $value);

                continue;
            }

            foreach ($this->getLocales() as $localeCode) {
                $productAttributesValues[] = $this->configureProductAttributeValue($productAttribute, $localeCode, $value);
            }
        }

        return $productAttributesValues;
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

    private function generateProductVariantName(ProductVariantInterface $variant): string
    {
        return trim(array_reduce(
            $variant->getOptionValues()->toArray(),
            static fn (?string $variantName, ProductOptionValueInterface $variantOption) => $variantName . sprintf('%s ', $variantOption->getValue()),
            '',
        ));
    }
}
