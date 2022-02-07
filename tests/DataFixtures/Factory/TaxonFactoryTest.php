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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactory;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TaxonFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_taxa(): void
    {
        $taxon = TaxonFactory::new()->create();

        $this->assertInstanceOf(TaxonInterface::class, $taxon->object());
    }

    /** @test */
    function it_creates_taxa_with_codes(): void
    {
        $taxon = TaxonFactory::new()->withCode('board-games')->create();

        $this->assertEquals('board-games', $taxon->getCode());

        $taxon = TaxonFactory::new()->create();

        $this->assertNotNull($taxon->getCode());
    }

    /** @test */
    function it_creates_taxa_with_translations_for_each_locales(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $taxon = TaxonFactory::new()->withName('Board games')->withoutPersisting()->create();

        $this->assertEquals(2, $taxon->getTranslations()->count());

        $taxon->setCurrentLocale('en_US');
        $taxon->setFallbackLocale('en_US');

        $this->assertEquals('Board games', $taxon->getName());
        $this->assertEquals('board-games', $taxon->getSlug());

        $taxon->setCurrentLocale('fr_fr');
        $taxon->setFallbackLocale('fr_FR');

        $this->assertEquals('Board games', $taxon->getName());
        $this->assertEquals('board-games', $taxon->getSlug());
    }

    /** @test */
    function it_creates_taxa_with_custom_translations(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $taxon = TaxonFactory::new()->withTranslations([
            'en_US' => [
                'name' => 'Board games',
            ],
            'fr_FR' => [
                'name' => 'Jeux de société',
            ],
        ])->withoutPersisting()->create();

        $taxon->setCurrentLocale('en_US');
        $taxon->setFallbackLocale('en_US');

        $this->assertEquals('Board games', $taxon->getName());
        $this->assertEquals('board-games', $taxon->getSlug());

        $taxon->setCurrentLocale('fr_fr');
        $taxon->setFallbackLocale('fr_FR');

        $this->assertEquals('Jeux de société', $taxon->getName());
        $this->assertEquals('jeux-de-societe', $taxon->getSlug());
    }

    /** @test */
    function it_creates_taxa_with_children(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();;

        $taxon = TaxonFactory::new()->withCode('categories')->withChildren([
            [
                'name' => 'Jeans',
            ],
            [
                'name' => 'Dresses',
            ],
        ])->withoutPersisting()->create();

        /** @var TaxonInterface $firstTaxon */
        $firstTaxon = $taxon->getChildren()->first();
        $this->assertCount(2, $taxon->getChildren());
        $this->assertEquals('Jeans', $firstTaxon->getName());
    }
}
