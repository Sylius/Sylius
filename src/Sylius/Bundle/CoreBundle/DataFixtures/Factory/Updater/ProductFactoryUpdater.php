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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductFactoryUpdater implements ProductFactoryUpdaterInterface
{
    public function __construct(private RepositoryInterface $localeRepository)
    {
    }

    public function update(ProductInterface $product, array $attributes): void
    {
        $product->setCode($attributes['code']);
        // $product->setVariantSelectionMethod($attributes['variant_selection_method']);
        $product->setName($attributes['name']);
        $product->setEnabled($attributes['enabled']);

        $this->createTranslations($product, $attributes);
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

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
