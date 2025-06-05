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

namespace Tests\Sylius\Bundle\CoreBundle\Fixture\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\TaxonExampleFactory;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class TaxonExampleFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $taxonFactory;

    private MockObject&TaxonRepositoryInterface $taxonRepository;

    private MockObject&RepositoryInterface $localeRepository;

    private MockObject&TaxonSlugGeneratorInterface $taxonSlugGenerator;

    private TaxonExampleFactory $factory;

    protected function setUp(): void
    {
        $this->taxonFactory = $this->createMock(FactoryInterface::class);
        $this->taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->taxonSlugGenerator = $this->createMock(TaxonSlugGeneratorInterface::class);
        $this->factory = new TaxonExampleFactory(
            $this->taxonFactory,
            $this->taxonRepository,
            $this->localeRepository,
            $this->taxonSlugGenerator,
        );
    }

    public function testItIsAnExampleFactory(): void
    {
        $this->assertInstanceOf(ExampleFactoryInterface::class, $this->factory);
    }

    public function testCreatesTranslationsForEachDefinedLocales(): void
    {
        $locale = $this->createMock(Locale::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $this->taxonFactory->expects($this->once())->method('createNew')->willReturn($taxon);
        $this->localeRepository->expects($this->once())->method('findAll')->willReturn([$locale]);
        $locale->expects($this->once())->method('getCode')->willReturn('fr_FR');
        $taxon->expects($this->once())->method('setCurrentLocale')->with('fr_FR');
        $taxon->expects($this->once())->method('setFallbackLocale')->with('fr_FR');
        $taxon->expects($this->once())->method('setCode')->with('Category');
        $taxon->expects($this->once())->method('setName')->with('Category');
        $taxon->expects($this->once())->method('setSlug')->with('category');
        $taxon->expects($this->once())->method('setDescription')->with($this->isType('string'));

        $this->factory->create([
            'name' => 'Category',
            'slug' => 'category',
        ]);
    }

    public function testCreatesTranslationsForEachCustomTranslations(): void
    {
        $locale1 = $this->createMock(Locale::class);
        $locale2 = $this->createMock(Locale::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $this->taxonFactory->expects($this->once())->method('createNew')->willReturn($taxon);
        $this->localeRepository->expects($this->once())->method('findAll')->willReturn([$locale1, $locale2]);
        $locale1->expects($this->any())->method('getCode')->willReturn('en_US');
        $locale2->expects($this->any())->method('getCode')->willReturn('fr_FR');

        $calledCurrentLocales = [];
        $taxon->expects($this->any())
            ->method('setCurrentLocale')
            ->willReturnCallback(function ($locale) use (&$calledCurrentLocales) {
                $calledCurrentLocales[] = $locale;
            })
        ;

        $calledFallbackLocales = [];
        $taxon->expects($this->any())
            ->method('setFallbackLocale')
            ->willReturnCallback(function ($locale) use (&$calledFallbackLocales) {
                $calledFallbackLocales[] = $locale;
            })
        ;

        $taxon->expects($this->once())->method('setCode')->with('Category');

        $calledNames = [];
        $taxon->expects($this->any())
            ->method('setName')
            ->willReturnCallback(function ($name) use (&$calledNames) {
                $calledNames[] = $name;
            })
        ;

        $calledSlugs = [];
        $taxon->expects($this->any())
            ->method('setSlug')
            ->willReturnCallback(function ($slug) use (&$calledSlugs) {
                $calledSlugs[] = $slug;
            })
        ;

        $taxon->expects($this->any())->method('setDescription')->with($this->isType('string'));

        $this->factory->create([
            'name' => 'Category',
            'slug' => 'category',
            'translations' => [
                'fr_FR' => [
                    'name' => 'Catégorie',
                    'slug' => 'categorie',
                ],
            ],
        ]);

        $this->assertContains('en_US', $calledCurrentLocales);
        $this->assertContains('fr_FR', $calledCurrentLocales);
        $this->assertContains('en_US', $calledFallbackLocales);
        $this->assertContains('fr_FR', $calledFallbackLocales);
        $this->assertContains('Category', $calledNames);
        $this->assertContains('Catégorie', $calledNames);
        $this->assertContains('category', $calledSlugs);
        $this->assertContains('categorie', $calledSlugs);
    }

    public function testReplacesExistingTranslationsForEachCustomTranslations(): void
    {
        $locale1 = $this->createMock(Locale::class);
        $locale2 = $this->createMock(Locale::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $this->taxonFactory->expects($this->once())->method('createNew')->willReturn($taxon);
        $this->localeRepository->expects($this->once())->method('findAll')->willReturn([$locale1, $locale2]);
        $locale1->expects($this->any())->method('getCode')->willReturn('en_US');
        $locale2->expects($this->any())->method('getCode')->willReturn('en_US');

        $calledCurrentLocales = [];
        $taxon->expects($this->any())
            ->method('setCurrentLocale')
            ->willReturnCallback(function ($locale) use (&$calledCurrentLocales) {
                $calledCurrentLocales[] = $locale;
            })
        ;

        $calledFallbackLocales = [];
        $taxon->expects($this->any())
            ->method('setFallbackLocale')
            ->willReturnCallback(function ($locale) use (&$calledFallbackLocales) {
                $calledFallbackLocales[] = $locale;
            })
        ;

        $taxon->expects($this->once())->method('setCode')->with('Category');

        $calledNames = [];
        $taxon->expects($this->any())
            ->method('setName')
            ->willReturnCallback(function ($name) use (&$calledNames) {
                $calledNames[] = $name;
            })
        ;

        $calledSlugs = [];
        $taxon->expects($this->any())
            ->method('setSlug')
            ->willReturnCallback(function ($slug) use (&$calledSlugs) {
                $calledSlugs[] = $slug;
            })
        ;

        $taxon->expects($this->any())->method('setDescription')->with($this->isType('string'));

        $this->factory->create([
            'name' => 'Category',
            'slug' => 'category',
            'translations' => [
                'en_US' => [
                    'name' => 'Categories',
                    'slug' => 'categories',
                ],
            ],
        ]);

        $this->assertContains('en_US', $calledCurrentLocales);
        $this->assertContains('en_US', $calledFallbackLocales);
        $this->assertContains('Category', $calledNames);
        $this->assertContains('Categories', $calledNames);
        $this->assertContains('category', $calledSlugs);
        $this->assertContains('categories', $calledSlugs);
    }
}
