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
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<ProductOptionInterface> */
class ProductOptionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected Generator $faker;

    protected OptionsResolver $optionsResolver;

    /**
     * @param FactoryInterface<ProductOptionInterface> $productOptionFactory
     * @param FactoryInterface<ProductOptionValueInterface> $productOptionValueFactory
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     */
    public function __construct(
        protected readonly FactoryInterface $productOptionFactory,
        protected readonly FactoryInterface $productOptionValueFactory,
        protected readonly RepositoryInterface $localeRepository,
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
            $productOptionValue->setPosition($value['position']);

            foreach ($this->getLocales() as $localeCode) {
                $productOptionValue->setCurrentLocale($localeCode);
                $productOptionValue->setFallbackLocale($localeCode);

                $productOptionValue->setValue($value['value']);
            }

            $productOption->addValue($productOptionValue);
        }

        foreach ($options['translations'] as $localeCode => $translation) {
            $productOption->setCurrentLocale($localeCode);
            $productOption->setFallbackLocale($localeCode);

            $productOption->setName($translation['name'] ?? $productOption->getName());

            if (isset($translation['values'])) {
                $this->updateValuesTranslations($productOption, $localeCode, $translation['values']);
            }
        }

        return $productOption;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', fn (Options $options): string => $this->faker->words(3, true))
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
            ->setDefault('values', null)
            ->setDefault('values', function (Options $options, ?array $values): array {
                if (is_array($values)) {
                    return $values;
                }

                $values = [];
                for ($i = 1; $i <= 5; ++$i) {
                    $values[sprintf('%s-option#%d', $options['code'], $i)] = ['value' => sprintf('%s #i%d', $options['name'], $i), 'position' => $i - 1];
                }

                if (!empty($options['translations'])) {
                    return [];
                }

                return $values;
            })
            ->setAllowedTypes('values', 'array')
            ->setDefault('translations', [])
            ->setAllowedTypes('translations', ['array'])
        ;
    }

    /**
     * @param array<string, string> $values
     */
    private function updateValuesTranslations(ProductOptionInterface $productOption, string $localeCode, array $values): void
    {
        foreach ($values as $code => $value) {
            $productOptionValue = $this->findOptionValueWithCode($productOption, $code);

            if (null === $productOptionValue) {
                /** @var ProductOptionValueInterface $productOptionValue */
                $productOptionValue = $this->productOptionValueFactory->createNew();
                $productOptionValue->setCode($code);
            }

            $productOptionValue->setCurrentLocale($localeCode);
            $productOptionValue->setFallbackLocale($localeCode);
            $productOptionValue->setValue($value);

            $productOption->addValue($productOptionValue);
        }
    }

    private function findOptionValueWithCode(ProductOptionInterface $productOption, string $code): ?ProductOptionValueInterface
    {
        foreach ($productOption->getValues() as $value) {
            if ($value->getCode() === $code) {
                return $value;
            }
        }

        return null;
    }

    /** @return iterable<string> */
    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
