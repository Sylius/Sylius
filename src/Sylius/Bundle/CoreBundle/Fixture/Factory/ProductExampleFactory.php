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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantImageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Generator\VariantGeneratorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductExampleFactory implements ExampleFactoryInterface
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
     * @var VariantGeneratorInterface
     */
    private $variantGenerator;

    /**
     * @var FactoryInterface
     */
    private $productVariantImageFactory;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

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
     * @param VariantGeneratorInterface $variantGenerator
     * @param FactoryInterface $productAttributeValueFactory
     * @param FactoryInterface $productVariantImageFactory
     * @param ImageUploaderInterface $imageUploader
     * @param RepositoryInterface $taxonRepository
     * @param RepositoryInterface $productAttributeRepository
     * @param RepositoryInterface $productOptionRepository
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        VariantGeneratorInterface $variantGenerator,
        FactoryInterface $productAttributeValueFactory,
        FactoryInterface $productVariantImageFactory,
        ImageUploaderInterface $imageUploader,
        RepositoryInterface $taxonRepository,
        RepositoryInterface $productAttributeRepository,
        RepositoryInterface $productOptionRepository,
        RepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository
    ) {
        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->variantGenerator = $variantGenerator;
        $this->productVariantImageFactory = $productVariantImageFactory;
        $this->imageUploader = $imageUploader;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
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

                ->setDefault('main_taxon', LazyOption::randomOne($taxonRepository))
                ->setAllowedTypes('main_taxon', ['null', 'string', TaxonInterface::class])
                ->setNormalizer('main_taxon', LazyOption::findOneBy($taxonRepository, 'code'))

                ->setDefault('taxons', LazyOption::randomOnes($taxonRepository, 3))
                ->setAllowedTypes('taxons', 'array')
                ->setNormalizer('taxons', LazyOption::findBy($taxonRepository, 'code'))

                ->setDefault('channels', LazyOption::randomOnes($channelRepository, 3))
                ->setAllowedTypes('channels', 'array')
                ->setNormalizer('channels', LazyOption::findBy($channelRepository, 'code'))

                ->setDefault('product_attributes', [])
                ->setAllowedTypes('product_attributes', 'array')
                ->setNormalizer('product_attributes', function (Options $options, array $productAttributes) use ($productAttributeRepository, $productAttributeValueFactory) {
                    $productAttributesValues = [];
                    foreach ($productAttributes as $code => $value) {
                        $productAttribute = $productAttributeRepository->findOneBy(['code' => $code]);

                        Assert::notNull($productAttribute);

                        /** @var AttributeValueInterface $productAttributeValue */
                        $productAttributeValue = $productAttributeValueFactory->createNew();
                        $productAttributeValue->setAttribute($productAttribute);
                        $productAttributeValue->setValue($value ?: $this->getRandomValueForProductAttribute($productAttribute));

                        $productAttributesValues[] = $productAttributeValue;
                    }

                    return $productAttributesValues;
                })

                ->setDefault('product_options', [])
                ->setAllowedTypes('product_options', 'array')
                ->setNormalizer('product_options', LazyOption::findBy($productOptionRepository, 'code'))

                ->setDefault('images', [])
                ->setAllowedTypes('images', 'array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);
        $product->setCode($options['code']);
        $product->setEnabled($options['enabled']);
        $product->setMainTaxon($options['main_taxon']);

        $product->setCreatedAt($this->faker->dateTimeBetween('-1 week', 'now'));

        foreach ($this->getLocales() as $localeCode) {
            $product->setCurrentLocale($localeCode);
            $product->setFallbackLocale($localeCode);

            $product->setName($options['name']);
            $product->setShortDescription($options['short_description']);
            $product->setDescription($options['description']);
        }

        foreach ($options['taxons'] as $taxon) {
            $product->addTaxon($taxon);
        }

        foreach ($options['channels'] as $channel) {
            $product->addChannel($channel);
        }

        foreach ($options['product_options'] as $option) {
            $product->addOption($option);
        }

        foreach ($options['product_attributes'] as $attribute) {
            $product->addAttribute($attribute);
        }

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
            $productVariant->setAvailableOn($this->faker->dateTimeThisYear);
            $productVariant->setPrice($this->faker->randomNumber(4));
            $productVariant->setCode(sprintf('%s-variant#%d', $options['code'], $i));
            $productVariant->setOnHand($this->faker->randomNumber(1));

            foreach ($options['images'] as $imagePath) {
                /** @var ProductVariantImageInterface $productVariantImage */
                $productVariantImage = $this->productVariantImageFactory->createNew();
                $productVariantImage->setFile(new UploadedFile($imagePath, basename($imagePath)));

                $this->imageUploader->upload($productVariantImage);

                $productVariant->addImage($productVariantImage);
            }

            ++$i;
        }

        return $product;
    }

    /**
     * @return array
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
     * @param AttributeInterface $productAttribute
     *
     * @return mixed
     */
    private function getRandomValueForProductAttribute(AttributeInterface $productAttribute)
    {
        switch ($productAttribute->getStorageType()) {
            case AttributeValueInterface::STORAGE_BOOLEAN:
                return $this->faker->boolean;
            case AttributeValueInterface::STORAGE_INTEGER:
                return $this->faker->numberBetween(0, 10000);
            case AttributeValueInterface::STORAGE_FLOAT:
                return $this->faker->randomFloat(4, 0, 10000);
            case AttributeValueInterface::STORAGE_TEXT:
                return $this->faker->sentence;
            case AttributeValueInterface::STORAGE_DATE:
            case AttributeValueInterface::STORAGE_DATETIME:
                return $this->faker->dateTimeThisCentury;
            default:
                throw new \BadMethodCallException();
        }
    }
}
