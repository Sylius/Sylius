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

namespace Tests\Sylius\Component\Taxonomy\Generator;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGenerator;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonSlugGeneratorTest extends TestCase
{
    /** @var TaxonInterface&MockObject */
    private MockObject $taxon;

    /** @var TaxonInterface&MockObject */
    private MockObject $parentTaxon;

    /** @var TaxonTranslationInterface&MockObject */
    private MockObject $taxonTranslation;

    /** @var TaxonTranslationInterface&MockObject */
    private MockObject $parentTaxonTranslation;

    private TaxonSlugGenerator $taxonSlugGenerator;

    protected function setUp(): void
    {
        $this->taxon = $this->createMock(TaxonInterface::class);
        $this->parentTaxon = $this->createMock(TaxonInterface::class);
        $this->taxonTranslation = $this->createMock(TaxonTranslationInterface::class);
        $this->parentTaxonTranslation = $this->createMock(TaxonTranslationInterface::class);
        $this->taxonSlugGenerator = new TaxonSlugGenerator();
    }

    public function testShouldImplementTaxonSlugGeneratorInterface(): void
    {
        $this->assertInstanceOf(TaxonSlugGeneratorInterface::class, $this->taxonSlugGenerator);
    }

    public function testShouldGenerateSlugForRootTaxon(): void
    {
        $this->taxon->expects($this->once())->method('getTranslation')->with('pl_PL')->willReturn($this->taxonTranslation);
        $this->taxonTranslation->expects($this->once())->method('getName')->willReturn('Board games');
        $this->taxon->expects($this->once())->method('getParent')->willReturn(null);

        $this->assertSame('board-games', $this->taxonSlugGenerator->generate($this->taxon, 'pl_PL'));
    }

    public function testShouldGenerateSlugForRootTaxonReplacingApostrophesWithHyphens(): void
    {
        $this->taxon->expects($this->once())->method('getTranslation')->with('pl_PL')->willReturn($this->taxonTranslation);
        $this->taxonTranslation->expects($this->once())->method('getName')->willReturn('Rock\'n\'roll');
        $this->taxon->expects($this->once())->method('getParent')->willReturn(null);

        $this->assertSame('rock-n-roll', $this->taxonSlugGenerator->generate($this->taxon, 'pl_PL'));
    }

    public function testShouldGenerateSlugForChildTaxonWhenParentTaxonAlreadyHasSlug(): void
    {
        $this->taxon->expects($this->once())->method('getTranslation')->with('pl_PL')->willReturn($this->taxonTranslation);
        $this->taxonTranslation->expects($this->once())->method('getName')->willReturn('Battle games');
        $this->taxon->expects($this->once())->method('getParent')->willReturn($this->parentTaxon);
        $this->parentTaxon->expects($this->once())->method('getTranslation')->with('pl_PL')->willReturn($this->parentTaxonTranslation);
        $this->parentTaxonTranslation->expects($this->once())->method('getSlug')->willReturn('board-games');

        $this->assertSame('board-games/battle-games', $this->taxonSlugGenerator->generate($this->taxon, 'pl_PL'));
    }

    public function testShouldGenerateSlugForChildTaxonEvenWhenParentTaxonDoesNotHaveSlug(): void
    {
        $this->taxon->expects($this->once())->method('getTranslation')->with('pl_PL')->willReturn($this->taxonTranslation);
        $this->taxonTranslation->expects($this->once())->method('getName')->willReturn('Battle games');
        $this->taxon->expects($this->once())->method('getParent')->willReturn($this->parentTaxon);
        $this->parentTaxon->expects($this->exactly(2))->method('getTranslation')->with('pl_PL')->willReturn($this->parentTaxonTranslation);
        $this->parentTaxonTranslation->expects($this->once())->method('getSlug')->willReturn(null);
        $this->parentTaxonTranslation->expects($this->once())->method('getName')->willReturn('Board games');
        $this->parentTaxon->expects($this->once())->method('getParent')->willReturn(null);

        $this->assertSame('board-games/battle-games', $this->taxonSlugGenerator->generate($this->taxon, 'pl_PL'));
    }

    public function testShouldThrowAnExceptionIfPassedTaxonHasNoName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->taxon->expects($this->once())->method('getTranslation')->with('pl_PL')->willReturn($this->taxonTranslation);
        $this->taxonTranslation->expects($this->once())->method('getName')->willReturn('');
        $this->taxon->expects($this->never())->method('getParent');

        $this->taxonSlugGenerator->generate($this->taxon, 'pl_PL');
    }
}
