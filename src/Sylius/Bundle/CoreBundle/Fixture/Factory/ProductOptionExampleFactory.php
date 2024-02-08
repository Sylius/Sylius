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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOptionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private FactoryInterface $productOptionFactory,
        private FactoryInterface $productOptionValueFactory,
        private RepositoryInterface $localeRepository,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ProductOptionInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ProductOptionInterface $productOption */
        $productOption = $this->productOptionFactory->createNew();
        $productOption->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $productOption->setCurrentLocale($localeCode);
            $productOption->setFallbackLocale($localeCode);

            $productOption->setName($options['name']);
        }

        foreach ($options['values'] as $code => $value) {
            /** @var ProductOptionValueInterface $productOptionValue */
            $productOptionValue = $this->productOptionValueFactory->createNew();
            $productOptionValue->setCode($code);

            foreach ($this->getLocales() as $localeCode) {
                $productOptionValue->setCurrentLocale($localeCode);
                $productOptionValue->setFallbackLocale($localeCode);

                $productOptionValue->setValue($value);
            }

            $productOption->addValue($productOptionValue);
        }

        return $productOption;
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
            ->setDefault('values', null)
            ->setDefault('values', function (Options $options, ?array $values): array {
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

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
