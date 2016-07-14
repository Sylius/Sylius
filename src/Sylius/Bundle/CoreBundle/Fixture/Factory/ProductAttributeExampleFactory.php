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

use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductAttributeExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var AttributeFactoryInterface
     */
    private $productAttributeFactory;

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
     * @param AttributeFactoryInterface $productAttributeFactory
     * @param RepositoryInterface $localeRepository
     * @param array $attributeTypes
     */
    public function __construct(
        AttributeFactoryInterface $productAttributeFactory,
        RepositoryInterface $localeRepository,
        array $attributeTypes
    ) {
        $this->productAttributeFactory = $productAttributeFactory;
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
                ->setDefault('type', function (Options $options) use ($attributeTypes) {
                    return $this->faker->randomElement(array_keys($attributeTypes));
                })
                ->setAllowedValues('type', array_keys($attributeTypes))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);
        
        /** @var OptionInterface $productAttribute */
        $productAttribute = $this->productAttributeFactory->createTyped($options['type']);
        $productAttribute->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $productAttribute->setCurrentLocale($localeCode);
            $productAttribute->setFallbackLocale($localeCode);

            $productAttribute->setName($options['name']);
        }

        return $productAttribute;
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
