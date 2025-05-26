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

namespace Tests\Sylius\Component\Taxonomy\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxonomy\Factory\TaxonFactory;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Sylius\Component\Taxonomy\Model\Taxon;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class TaxonFactoryTest extends TestCase
{
    /** @var FactoryInterface<Taxon>&MockObject */
    private MockObject $factory;

    /** @var TaxonInterface&MockObject */
    private MockObject $taxon;

    /** @var TaxonFactory<TaxonInterface> */
    private TaxonFactory $taxonFactory;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->taxon = $this->createMock(TaxonInterface::class);
        $this->taxonFactory = new TaxonFactory($this->factory);
    }

    public function testShouldImplementTaxonFactoryInterface(): void
    {
        $this->assertInstanceOf(TaxonFactoryInterface::class, $this->taxonFactory);
    }

    public function testShouldUseDecoratedFactoryToCreateNewTaxon(): void
    {
        $this->factory->expects($this->once())->method('createNew')->willReturn($this->taxon);

        $this->assertSame($this->taxon, $this->taxonFactory->createNew());
    }

    public function testShouldCreateTaxonForGivenParentTaxon(): void
    {
        $parent = $this->createMock(TaxonInterface::class);

        $this->factory->expects($this->once())->method('createNew')->willReturn($this->taxon);
        $this->taxon->expects($this->once())->method('setParent')->with($parent);

        $this->assertSame($this->taxon, $this->taxonFactory->createForParent($parent));
    }
}
