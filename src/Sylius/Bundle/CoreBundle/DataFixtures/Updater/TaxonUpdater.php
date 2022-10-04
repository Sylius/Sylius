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
use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\TaxonDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\GetLocalesTrait;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;

final class TaxonUpdater implements TaxonUpdaterInterface
{
    use GetLocalesTrait;

    private Generator $faker;

    public function __construct(
        private TaxonSlugGeneratorInterface $taxonSlugGenerator,
        private TaxonDefaultValuesInterface $defaultValues,
        private FactoryInterface $taxonFactory,
        private LocaleFactoryInterface $localeFactory,
    ) {
        $this->faker = Factory::create();
    }

    public function update(TaxonInterface $taxon, array $attributes): void
    {
        $code = $attributes['code'];

        $taxon->setCode($code);

        if (null !== $parentTaxon = $attributes['parent']) {
            $taxon->setParent($parentTaxon);
        }

        // add translation for each defined locales
        foreach ($this->getLocales() as $localeCode) {
            $this->createTranslation($taxon, $localeCode, $attributes);
        }

        // create or replace with custom translations
        foreach ($attributes['translations'] as $localeCode => $translationAttributes) {
            $this->createTranslation($taxon, $localeCode, array_merge($attributes, $translationAttributes));
        }
    }

    private function createTranslation(TaxonInterface $taxon, string $localeCode, array $attributes = []): void
    {
        $taxon->setCurrentLocale($localeCode);
        $taxon->setFallbackLocale($localeCode);

        $taxon->setName($attributes['name']);
        $taxon->setDescription($attributes['description']);
        $taxon->setSlug($attributes['slug'] ?: $this->taxonSlugGenerator->generate($taxon, $localeCode));
    }
}
