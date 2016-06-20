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
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ArchetypeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductArchetypeFixture extends AbstractResourceFixture
{
    /**
     * @var FactoryInterface
     */
    private $productArchetypeFactory;

    /**
     * @var RepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @var RepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param FactoryInterface $productArchetypeFactory
     * @param ObjectManager $productArchetypeManager
     * @param RepositoryInterface $productOptionRepository
     * @param RepositoryInterface $productAttributeRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $productArchetypeFactory,
        ObjectManager $productArchetypeManager,
        RepositoryInterface $productOptionRepository,
        RepositoryInterface $productAttributeRepository,
        RepositoryInterface $localeRepository
    ) {
        parent::__construct($productArchetypeManager, 'product_archetypes', 'name');

        $this->productArchetypeFactory = $productArchetypeFactory;
        $this->productOptionRepository = $productOptionRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product_archetype';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var ArchetypeInterface $productArchetype */
        $productArchetype = $this->productArchetypeFactory->createNew();
        $productArchetype->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $productArchetype->setCurrentLocale($localeCode);
            $productArchetype->setFallbackLocale($localeCode);

            $productArchetype->setName(sprintf('[%s] %s', $localeCode, $options['name']));
        }

        foreach ($options['product_options'] as $productOption) {
            $productArchetype->addOption($productOption);
        }

        foreach ($options['product_attributes'] as $productAttribute) {
            $productArchetype->addAttribute($productAttribute);
        }

        return $productArchetype;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->arrayNode('product_options')->prototype('scalar')->end()->end()
                ->arrayNode('product_attributes')->prototype('scalar')->end()->end()
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
            ->setDefault('product_options', [])
            ->setAllowedTypes('product_options', 'array')
            ->setNormalizer('product_options', static::createLimitedResourcesNormalizer($this->productOptionRepository, 2))
            ->setDefault('product_attributes', [])
            ->setAllowedTypes('product_attributes', 'array')
            ->setNormalizer('product_attributes', static::createLimitedResourcesNormalizer($this->productAttributeRepository, 2))
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
}
