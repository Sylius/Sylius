<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Fixture\Factory;

use Sylius\Core\Formatter\StringInflector;
use Sylius\Locale\Model\LocaleInterface;
use Sylius\Product\Model\OptionInterface;
use Sylius\Product\Model\OptionValueInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductOptionExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $productOptionFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionValueFactory;

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
     * @param FactoryInterface $productOptionFactory
     * @param FactoryInterface $productOptionValueFactory
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        RepositoryInterface $localeRepository
    ) {
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
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
                ->setDefault('values', null)
                ->setDefault('values', function (Options $options, $values) {
                    if (is_array($values)) {
                        return $values;
                    }

                    $values = [];
                    for ($i = 1; $i <= 5; ++$i) {
                        $values[sprintf('%s-option#%d', $options['code'], $i)] = sprintf('%s #i%d', $options['name'], $i);
                    }

                    return $values;
                })
                ->setAllowedTypes('values', 'array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);
        
        /** @var OptionInterface $productOption */
        $productOption = $this->productOptionFactory->createNew();
        $productOption->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $productOption->setCurrentLocale($localeCode);
            $productOption->setFallbackLocale($localeCode);

            $productOption->setName(sprintf('[%s] %s', $localeCode, $options['name']));
        }

        foreach ($options['values'] as $code => $value) {
            /** @var OptionValueInterface $productOptionValue */
            $productOptionValue = $this->productOptionValueFactory->createNew();
            $productOptionValue->setCode($code);

            foreach ($this->getLocales() as $localeCode) {
                $productOptionValue->setCurrentLocale($localeCode);
                $productOptionValue->setFallbackLocale($localeCode);

                $productOptionValue->setValue(sprintf('[%s] %s', $localeCode, $value));

            }

            $productOption->addValue($productOptionValue);
        }

        return $productOption;
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
