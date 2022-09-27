<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class ProductUpdater implements ProductUpdaterInterface
{
    private Generator $faker;

    public function __construct(
        private RepositoryInterface $localeRepository,
        private FactoryInterface $productVariantFactory,
        private FactoryInterface $channelPricingFactory,
        private ProductVariantGeneratorInterface $variantGenerator,
        private RepositoryInterface $channelRepository,
    ) {
        $this->faker = Factory::create();
    }

    public function update(ProductInterface $product, array $attributes): void
    {
        $product->setCode($attributes['code']);
        $product->setVariantSelectionMethod($attributes['variant_selection_method']);
        $product->setName($attributes['name']);
        $product->setEnabled($attributes['enabled']);
        $this->createTranslations($product, $attributes);
        $this->createVariants($product, $attributes);
    }

    private function createTranslations(ProductInterface $product, array $attributes): void
    {
        foreach ($this->getLocales() as $localeCode) {
            $product->setCurrentLocale($localeCode);
            $product->setFallbackLocale($localeCode);

            $product->setName($attributes['name']);
            $product->setSlug($attributes['slug']);
            $product->setShortDescription($attributes['short_description']);
            $product->setDescription($attributes['description']);
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

            foreach ($this->channelRepository->findAll() as $channel) {
                $this->createChannelPricings($productVariant, $channel->getCode());
            }

            ++$i;
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

    private function createChannelPricings(ProductVariantInterface $productVariant, string $channelCode): void
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setChannelCode($channelCode);
        $channelPricing->setPrice($this->faker->numberBetween(100, 10000));

        $productVariant->addChannelPricing($channelPricing);
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
