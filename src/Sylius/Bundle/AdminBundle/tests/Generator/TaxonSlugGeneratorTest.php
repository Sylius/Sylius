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

namespace Tests\Sylius\Bundle\AdminBundle\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Generator\TaxonSlugGenerator;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface as BaseTaxonSlugGeneratorInterface;

final class TaxonSlugGeneratorTest extends TestCase
{
    private BaseTaxonSlugGeneratorInterface&MockObject $baseSlugGenerator;

    private TaxonSlugGenerator $slugGenerator;

    protected function setUp(): void
    {
        $this->baseSlugGenerator = $this->createMock(BaseTaxonSlugGeneratorInterface::class);
        $this->slugGenerator = new TaxonSlugGenerator($this->baseSlugGenerator);
    }

    public function testGeneratesSlugForTaxonName(): void
    {
        $this->baseSlugGenerator->expects($this->never())->method('generate');

        $result = $this->slugGenerator->generate('Board games', 'pl_PL');

        $this->assertSame('board-games', $result);
    }

    public function testGeneratesSlugForTaxonNameReplacingApostrophesWithHyphens(): void
    {
        $this->baseSlugGenerator->expects($this->never())->method('generate');

        $result = $this->slugGenerator->generate("Rock'n'roll", 'pl_PL');

        $this->assertSame('rock-n-roll', $result);
    }

    public function testGeneratesSlugForTaxonNameAndItsParent(): void
    {
        $parentTaxon = $this->createMock(TaxonInterface::class);

        $this->baseSlugGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($parentTaxon, 'pl_PL')
            ->willReturn('games');

        $result = $this->slugGenerator->generate('Board games', 'pl_PL', $parentTaxon);

        $this->assertSame('games/board-games', $result);
    }
}
