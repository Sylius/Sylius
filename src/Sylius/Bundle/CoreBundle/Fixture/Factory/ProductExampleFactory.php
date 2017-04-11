<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @var FactoryInterface
     */
    private $productVariantFactory;

    /**
     * @var FactoryInterface
     */
    private $channelPricingFactory;

    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @var ProductVariantGeneratorInterface
     */
    private $variantGenerator;

    /**
     * @var FactoryInterface
     */
    private $productAttributeValueFactory;

    /**
     * @var FactoryInterface
     */
    private $productImageFactory;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;

    /**
     * @var RepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var RepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var RepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @var RepositoryInterface
     */
    private $channelRepository;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $productFactory
     * @param FactoryInterface $productVariantFactory
     * @param FactoryInterface $channelPricing
     * @param ProductVariantGeneratorInterface $variantGenerator
     * @param FactoryInterface $productAttributeValueFactory
     * @param FactoryInterface $productImageFactory
     * @param FactoryInterface $productTaxonFactory
     * @param ImageUploaderInterface $imageUploader
     * @param SlugGeneratorInterface $slugGenerator
     * @param RepositoryInterface $taxonRepository
     * @param RepositoryInterface $productAttributeRepository
     * @param RepositoryInterface $productOptionRepository
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $channelPricing,
        ProductVariantGeneratorInterface $variantGenerator,
        FactoryInterface $productAttributeValueFactory,
        FactoryInterface $productImageFactory,
        FactoryInterface $productTaxonFactory,
        ImageUploaderInterface $imageUploader,
        SlugGeneratorInterface $slugGenerator,
        RepositoryInterface $taxonRepository,
        RepositoryInterface $productAttributeRepository,
        RepositoryInterface $productOptionRepository,
        RepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository
    ) {
        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->channelPricingFactory = $channelPricing;
        $this->variantGenerator = $variantGenerator;
        $this->productAttributeValueFactory = $productAttributeValueFactory;
        $this->productImageFactory = $productImageFactory;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->imageUploader = $imageUploader;
        $this->slugGenerator = $slugGenerator;
        $this->taxonRepository = $taxonRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
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

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('name', function (Options $options) {
                return $this->faker->words(3, true);
            })

            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })

            ->setDefault('enabled', function (Options $options) {
                return $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')

            ->setDefault('short_description', function (Options $options) {
                return $this->faker->paragraph;
            })

            ->setDefault('description', function (Options $options) {
                return $this->faker->paragraphs(3, true);
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
            ->setAllowedValues('variant_selection_method', [ProductInterface::VARIANT_SELECTION_MATCH, ProductInterface::VARIANT_SELECTION_CHOICE])

            ->setDefault('product_attributes', [])
            ->setAllowedTypes('product_attributes', 'array')
            ->setNormalizer('product_attributes', function (Options $options, array $productAttributes) {
                $productAttributesValues = [];
                foreach ($productAttributes as $code => $value) {
                    foreach ($this->getLocales() as $localeCode) {
                        /** @var ProductAttributeInterface $productAttribute */
                        $productAttribute = $this->productAttributeRepository->findOneBy(['code' => $code]);

                        Assert::notNull($productAttribute);

                        /** @var ProductAttributeValueInterface $productAttributeValue */
                        $productAttributeValue = $this->productAttributeValueFactory->createNew();
                        $productAttributeValue->setAttribute($productAttribute);
                        $productAttributeValue->setValue($value ?: $this->getRandomValueForProductAttribute($productAttribute));
                        $productAttributeValue->setLocaleCode($localeCode);

                        $productAttributesValues[] = $productAttributeValue;
                    }
                }

                return $productAttributesValues;
            })

            ->setDefault('product_options', [])
            ->setAllowedTypes('product_options', 'array')
            ->setNormalizer('product_options', LazyOption::findBy($this->productOptionRepository, 'code'))

            ->setDefault('images', [])
            ->setAllowedTypes('images', 'array')

            ->setDefault('shipping_required', true)
        ;
    }

    /**
     * @param ProductInterface $product
     * @param array $options
     */
    private function createTranslations(ProductInterface $product, array $options)
    {
        foreach ($this->getLocales() as $localeCode) {
            $product->setCurrentLocale($localeCode);
            $product->setFallbackLocale($localeCode);

            $product->setName($options['name']);
            $product->setSlug($this->slugGenerator->generate($options['name']));
            $product->setShortDescription($options['short_description']);
            $product->setDescription($options['description']);
        }
    }

    /**
     * @param ProductInterface $product
     * @param array $options
     */
    private function createRelations(ProductInterface $product, array $options)
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

    /**
     * @param ProductInterface $product
     * @param array $options
     */
    private function createVariants(ProductInterface $product, array $options)
    {
        try {
            $this->variantGenerator->generate($product);
        } catch (\InvalidArgumentException $exception) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $this->productVariantFactory->createNew();

            $product->addVariant($productVariant);
        }

        $i = 0;
        /** @var ProductVariantInterface $productVariant */
        foreach ($product->getVariants() as $productVariant) {
            $productVariant->setName($this->faker->word);
            $productVariant->setCode(sprintf('%s-variant-%d', $options['code'], $i));
            $productVariant->setOnHand($this->faker->randomNumber(1));
            $productVariant->setShippingRequired($options['shipping_required']);

            foreach ($this->channelRepository->findAll() as $channel) {
                $this->createChannelPricings($productVariant, $channel->getCode());
            }

            ++$i;
        }
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @param string $channelCode
     */
    private function createChannelPricings(ProductVariantInterface $productVariant, $channelCode)
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setChannelCode($channelCode);
        $channelPricing->setPrice($this->faker->randomNumber(3));

        $productVariant->addChannelPricing($channelPricing);
    }

    /**
     * @param ProductInterface $product
     * @param array $options
     */
    private function createImages(ProductInterface $product, array $options)
    {
        foreach ($options['images'] as $image) {
            $imagePath = array_shift($image);
            $uploadedImage = new UploadedFile($imagePath, basename($imagePath));

            /** @var ImageInterface $productImage */
            $productImage = $this->productImageFactory->createNew();
            $productImage->setFile($uploadedImage);
            $productImage->setType(end($image) ?: null);

            $this->imageUploader->upload($productImage);

            $product->addImage($productImage);
        }
    }

    /**
     * @param ProductInterface $product
     * @param array $options
     */
    private function createProductTaxons(ProductInterface $product, array $options)
    {
        foreach ($options['taxons'] as $taxon) {
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setProduct($product);
            $productTaxon->setTaxon($taxon);

            $product->addProductTaxon($productTaxon);
        }
    }

    /**
     * @return \Generator
     */
    private function getLocales()
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    /**
     * @param ProductAttributeInterface $productAttribute
     *
     * @return mixed
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
                if ($productAttribute->getType() == SelectAttributeType::TYPE) {
                    if ($productAttribute->getConfiguration()['multiple']) {
                        return $this->faker->randomElements(
                            array_keys($productAttribute->getConfiguration()['choices']),
                            $this->faker->numberBetween(1, count($productAttribute->getConfiguration()['choices']))
                        );
                    }

                    return [$this->faker->randomKey($productAttribute->getConfiguration()['choices'])];
                }
            default:
                throw new \BadMethodCallException();
        }
    }
}
