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
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ArchetypeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductArchetypeExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $productArchetypeFactory;

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
     * @param FactoryInterface $productArchetypeFactory
     * @param RepositoryInterface $productOptionRepository
     * @param RepositoryInterface $productAttributeRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $productArchetypeFactory,
        RepositoryInterface $productOptionRepository,
        RepositoryInterface $productAttributeRepository,
        RepositoryInterface $localeRepository
    ) {
        $this->productArchetypeFactory = $productArchetypeFactory;
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
                ->setDefault('product_options', LazyOption::randomOnes($productOptionRepository, 2))
                ->setAllowedTypes('product_options', 'array')
                ->setNormalizer('product_options', LazyOption::findBy($productOptionRepository, 'code'))
                ->setDefault('product_attributes', LazyOption::randomOnes($productAttributeRepository, 2))
                ->setAllowedTypes('product_attributes', 'array')
                ->setNormalizer('product_attributes', LazyOption::findBy($productAttributeRepository, 'code'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);
        
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
