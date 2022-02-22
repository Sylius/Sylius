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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<TaxonInterface>
 *
 * @method static TaxonInterface|Proxy createOne(array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TaxonInterface|Proxy find(object|array|mixed $criteria)
 * @method static TaxonInterface|Proxy findOrCreate(array $attributes)
 * @method static TaxonInterface|Proxy first(string $sortedField = 'id')
 * @method static TaxonInterface|Proxy last(string $sortedField = 'id')
 * @method static TaxonInterface|Proxy random(array $attributes = [])
 * @method static TaxonInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] all()
 * @method static TaxonInterface[]|Proxy[] findBy(array $attributes)
 * @method static TaxonInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method TaxonInterface|Proxy create(array|callable $attributes = [])
 */
class TaxonFactory extends ModelFactory implements TaxonFactoryInterface
{
    public function __construct(
        private FactoryInterface $taxonFactory,
        private RepositoryInterface $taxonRepository,
        private RepositoryInterface $localeRepository,
        private TaxonSlugGeneratorInterface $taxonSlugGenerator
    ) {
        parent::__construct();
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    public function withName(string $name): self
    {
        return $this->addState(['name' => $name]);
    }

    public function withSlug(string $slug): self
    {
        return $this->addState(['slug' => $slug]);
    }

    public function withDescription(string $description): self
    {
        return $this->addState(['description' => $description]);
    }

    public function withTranslations(array $translations): self
    {
        return $this->addState(['translations' => $translations]);
    }

    public function withChildren(array $children): self
    {
        return $this->addState(['children' => $children]);
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->unique()->words(3, true),
            'code' => null,
            'slug' => null,
            'description' => self::faker()->paragraph,
            'translations' => [],
            'children' => [],
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): TaxonInterface {
                return $this->createTaxon($attributes);
            })
        ;
    }

    protected function createTaxon(array $attributes = [], ?TaxonInterface $parentTaxon = null): ?TaxonInterface
    {
        $attributes = array_merge($this->getDefaults(), $attributes);

        $code = $attributes['code'] ?: StringInflector::nameToCode($attributes['name']);

        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);

        if (null === $taxon) {
            /** @var TaxonInterface $taxon */
            $taxon = $this->taxonFactory->createNew();
        }

        $taxon->setCode($code);

        if (null !== $parentTaxon) {
            $taxon->setParent($parentTaxon);
        }

        // add translation for each defined locales
        foreach ($this->getLocales() as $localeCode) {
            $this->createTranslation($taxon, $localeCode, $attributes);
        }

        // create or replace with custom translations
        foreach ($attributes['translations'] as $localeCode => $translationAttributes) {
            $this->createTranslation($taxon, $localeCode, $translationAttributes);
        }

        foreach ($attributes['children'] as $childAttributes) {
            $this->createTaxon($childAttributes, $taxon);
        }

        return $taxon;
    }

    protected function createTranslation(TaxonInterface $taxon, string $localeCode, array $attributes = []): void
    {
        $attributes = array_merge($this->getDefaults(), $attributes);

        $taxon->setCurrentLocale($localeCode);
        $taxon->setFallbackLocale($localeCode);

        $taxon->setName($attributes['name']);
        $taxon->setDescription($attributes['description']);
        $taxon->setSlug($attributes['slug'] ?: $this->taxonSlugGenerator->generate($taxon, $localeCode));
    }

    protected static function getClass(): string
    {
        return Taxon::class;
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
