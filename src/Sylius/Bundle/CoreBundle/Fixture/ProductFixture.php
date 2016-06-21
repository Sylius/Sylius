<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ArchetypeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Variation\Generator\VariantGeneratorInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductFixture extends AbstractResourceFixture
{
    /**
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @var VariantGeneratorInterface
     */
    private $variantGenerator;

    /**
     * @var FactoryInterface
     */
    private $productAttibuteValueFactory;

    /**
     * @var RepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var RepositoryInterface
     */
    private $archetypeRepository;

    /**
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;

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
     * @param FactoryInterface $productFactory
     * @param ObjectManager $productManager
     * @param VariantGeneratorInterface $variantGenerator
     * @param FactoryInterface $productAttibuteValueFactory
     * @param RepositoryInterface $taxonRepository
     * @param RepositoryInterface $archetypeRepository
     * @param RepositoryInterface $shippingCategoryRepository
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $productFactory,
        ObjectManager $productManager,
        VariantGeneratorInterface $variantGenerator,
        FactoryInterface $productAttibuteValueFactory,
        RepositoryInterface $taxonRepository,
        RepositoryInterface $archetypeRepository,
        RepositoryInterface $shippingCategoryRepository,
        RepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository
    ) {
        parent::__construct($productManager, 'products', 'name');

        $this->productFactory = $productFactory;
        $this->variantGenerator = $variantGenerator;
        $this->productAttibuteValueFactory = $productAttibuteValueFactory;
        $this->taxonRepository = $taxonRepository;
        $this->archetypeRepository = $archetypeRepository;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);
        $product->setCode($options['code']);
        $product->setEnabled($options['enabled']);
        $product->setMainTaxon($options['main_taxon']);
        $product->setArchetype($options['archetype']);
        $product->setShippingCategory($options['shipping_category']);

        foreach ($this->getLocales() as $localeCode) {
            $product->setCurrentLocale($localeCode);
            $product->setFallbackLocale($localeCode);

            $product->setName(sprintf('[%s] %s', $localeCode, $options['name']));
            $product->setShortDescription(sprintf('[%s] %s', $localeCode, $options['short_description']));
            $product->setDescription(sprintf('[%s] %s', $localeCode, $options['description']));
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

        $this->variantGenerator->generate($product);

        $i = 0;
        /** @var ProductVariantInterface $productVariant */
        foreach ($product->getVariants() as $productVariant) {
            $productVariant->setAvailableOn($this->faker->dateTimeThisYear);
            $productVariant->setPrice($this->faker->randomNumber(4));
            $productVariant->setCode(sprintf('%s-variant#%d', $options['code'], $i));
            $productVariant->setOnHand($this->faker->randomNumber(1));

            ++$i;
        }

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('short_description')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->scalarNode('main_taxon')->cannotBeEmpty()->end()
                ->scalarNode('archetype')->cannotBeEmpty()->end()
                ->scalarNode('shipping_category')->cannotBeEmpty()->end()
                ->arrayNode('taxons')->prototype('scalar')->end()->end()
                ->arrayNode('channels')->prototype('scalar')->end()->end()
                ->arrayNode('product_attributes')->prototype('scalar')->end()->end()
                ->arrayNode('product_options')->prototype('scalar')->end()->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceOptionsResolver(array $options, OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired('name')
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
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('main_taxon', null)
            ->setAllowedTypes('main_taxon', ['null', 'string', TaxonInterface::class])
            ->setNormalizer('main_taxon', static::createResourceNormalizer($this->taxonRepository))
            ->setDefault('archetype', null)
            ->setAllowedTypes('archetype', ['null', 'string', ArchetypeInterface::class])
            ->setNormalizer('archetype', static::createResourceNormalizer($this->archetypeRepository))
            ->setDefault('shipping_category', null)
            ->setAllowedTypes('shipping_category', ['null', 'string', ShippingCategoryInterface::class])
            ->setNormalizer('shipping_category', static::createResourceNormalizer($this->shippingCategoryRepository))
            ->setDefault('taxons', [])
            ->setAllowedTypes('taxons', 'array')
            ->setNormalizer('taxons', static::createResourcesNormalizer($this->taxonRepository))
            ->setDefault('channels', [])
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', static::createResourcesNormalizer($this->channelRepository))
            ->setDefault('product_attributes', [])
            ->setAllowedTypes('product_attributes', 'array')
            ->setNormalizer('product_attributes', function (Options $options, array $productAttributes) {
                /** @var ArchetypeInterface $archetype */
                $archetype = $options['archetype'];

                return array_map(
                    function (AttributeInterface $productAttribute) {
                        /** @var AttributeValueInterface $productAttributeValue */
                        $productAttributeValue = $this->productAttibuteValueFactory->createNew();
                        $productAttributeValue->setAttribute($productAttribute);
                        $productAttributeValue->setValue($this->getRandomValueForProductAttribute($productAttribute));

                        return $productAttributeValue;
                    },
                    array_merge($productAttributes, $archetype->getAttributes()->toArray())
                );
            })
            ->setDefault('product_options', [])
            ->setAllowedTypes('product_options', 'array')
            ->setNormalizer('product_options', function (Options $options, array $productOptions) {
                /** @var ArchetypeInterface $archetype */
                $archetype = $options['archetype'];

                return array_merge($productOptions, $archetype->getOptions()->toArray());
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResourcesOptions($amount)
    {
        $resourcesOptions = [];
        for ($i = 0; $i < (int) $amount; ++$i) {
            $resourcesOptions[] = ['name' => $this->faker->words(3, true)];
        }

        return $resourcesOptions;
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
