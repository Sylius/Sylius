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

namespace Tests\Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxonomy\Model\Taxon;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonTest extends TestCase
{
    /** @var TaxonInterface&MockObject */
    private MockObject $categoryTaxon;

    /** @var TaxonInterface&MockObject */
    private MockObject $tshirtTaxon;

    private Taxon $taxon;

    protected function setUp(): void
    {
        $this->categoryTaxon = $this->createMock(TaxonInterface::class);
        $this->tshirtTaxon = $this->createMock(TaxonInterface::class);
        $this->taxon = new Taxon();
        $this->taxon->setCurrentLocale('pl_PL');
        $this->taxon->setFallbackLocale('en_US');
    }

    public function testShouldImplementTaxonInterface(): void
    {
        $this->assertInstanceOf(TaxonInterface::class, $this->taxon);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        $this->assertNull($this->taxon->getId());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->taxon->setCode('TX2');

        $result = $this->taxon->getCode();

        $this->assertEquals('TX2', $result);
    }

    public function testShouldHaveNoParentByDefault(): void
    {
        $this->assertNull($this->taxon->getParent());
    }

    public function testShouldParentBeMutable(): void
    {
        $this->taxon->setParent($this->categoryTaxon);

        $this->assertSame($this->categoryTaxon, $this->taxon->getParent());
    }

    public function testShouldBeRootByDefault(): void
    {
        $this->assertTrue($this->taxon->isRoot());
    }

    public function testShouldNotBeRootIfHaveParent(): void
    {
        $this->taxon->setParent($this->categoryTaxon);

        $this->assertFalse($this->taxon->isRoot());
    }

    public function testShouldBeRootIfHaveNoParent(): void
    {
        $this->taxon->setParent(null);

        $this->assertTrue($this->taxon->isRoot());
    }

    public function testShouldReturnListOfAncestors(): void
    {
        $this->tshirtTaxon->expects($this->exactly(3))->method('getParent')->willReturn($this->categoryTaxon);
        $this->tshirtTaxon->expects($this->once())->method('addChild')->with($this->taxon);

        $this->taxon->setParent($this->tshirtTaxon);

        $this->assertCount(2, $this->taxon->getAncestors());
        $this->assertContains($this->categoryTaxon, $this->taxon->getAncestors());
        $this->assertContains($this->tshirtTaxon, $this->taxon->getAncestors());
    }

    public function testShouldReturnListWithSingleAncestor(): void
    {
        $this->categoryTaxon->expects($this->once())->method('addChild')->with($this->taxon);
        $this->categoryTaxon->expects($this->exactly(2))->method('getParent')->willReturn(null);

        $this->taxon->setParent($this->categoryTaxon);

        $this->assertCount(1, $this->taxon->getAncestors());
        $this->assertContains($this->categoryTaxon, $this->taxon->getAncestors());
    }

    public function testShouldReturnAnEmptyListOfAncestorsIfCalledOnRootTaxon(): void
    {
        $this->assertTrue($this->taxon->isRoot());
        $this->assertTrue($this->taxon->getAncestors()->isEmpty());
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        $this->assertNull($this->taxon->getName());
    }

    public function testShouldNameBeMutable(): void
    {
        $name = 'Brand';

        $this->taxon->setName($name);

        $this->assertSame($name, $this->taxon->getName());
    }

    public function testShouldReturnNameWhenConvertedToString(): void
    {
        $name = 'T-Shirt material';

        $this->taxon->setName($name);

        $this->assertSame($name, (string) $this->taxon);
    }

    public function testShouldFullNameBeNullIfUnnamed(): void
    {
        $this->assertNull($this->taxon->getFullname());
    }

    public function testShouldFullNameBeEqualNameIfNoParent(): void
    {
        $name = 'Category';

        $this->taxon->setName($name);

        $this->assertSame($name, $this->taxon->getFullname());
    }

    public function testFullNamePrependsWithParentsFullName(): void
    {
        $this->tshirtTaxon->expects($this->once())->method('getFullname')->with(' / ')->willReturn('Category / T-shirts');
        $this->tshirtTaxon->expects($this->once())->method('addChild')->with($this->taxon);

        $this->taxon->setName('Men');
        $this->taxon->setParent($this->tshirtTaxon);

        $this->assertSame('Category / T-shirts / Men', $this->taxon->getFullname());
    }

    public function testShouldHaveNoDescriptionByDefault(): void
    {
        $this->assertNull($this->taxon->getDescription());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->taxon->setDescription('This is a list of brands.');

        $this->assertSame('This is a list of brands.', $this->taxon->getDescription());
    }

    public function testShouldHaveNoSlugByDefault(): void
    {
        $this->assertNull($this->taxon->getSlug());
    }

    public function testShouldSlugBeMutable(): void
    {
        $this->taxon->setSlug('t-shirts');

        $this->assertSame('t-shirts', $this->taxon->getSlug());
    }

    public function testShouldInitializeChildTaxonCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->taxon->getChildren());
    }

    public function testShouldAllowToCheckIfGivenTaxonIsItsChild(): void
    {
        $this->assertFalse($this->taxon->hasChild($this->tshirtTaxon));
    }

    public function testShouldAllowToAddChildTaxons(): void
    {
        $this->tshirtTaxon->expects($this->once())->method('getParent')->willReturn(null);
        $this->tshirtTaxon->expects($this->once())->method('setParent')->with($this->taxon);

        $this->taxon->addChild($this->tshirtTaxon);

        $this->assertCount(1, $this->taxon->getChildren());
        $this->assertContains($this->tshirtTaxon, $this->taxon->getChildren());
    }

    public function testShouldAllowToRemoveChildTaxons(): void
    {
        $this->taxon->addChild($this->tshirtTaxon);
        $this->tshirtTaxon->expects($this->once())->method('setParent')->with(null);

        $this->taxon->removeChild($this->tshirtTaxon);

        $this->assertCount(0, $this->taxon->getChildren());
    }

    public function testShouldHavePosition(): void
    {
        $this->taxon->setPosition(0);

        $this->assertSame(0, $this->taxon->getPosition());
    }

    public function testShouldHaveNoChildrenByDefault(): void
    {
        $this->assertFalse($this->taxon->hasChildren());
        $this->assertTrue($this->taxon->getChildren()->isEmpty());
    }

    public function testShouldHaveChildrenWhenChildrenHasBeenAdded(): void
    {
        $this->taxon->addChild($this->tshirtTaxon);

        $this->assertTrue($this->taxon->hasChildren());
        $this->assertCount(1, $this->taxon->getChildren());
    }

    public function testShouldReturnEnabledChildren(): void
    {
        $this->categoryTaxon->expects($this->exactly(2))->method('isEnabled')->willReturn(true);
        $this->categoryTaxon->expects($this->once())->method('getParent')->willReturn(null);
        $this->categoryTaxon->expects($this->once())->method('setParent')->with($this->taxon);
        $this->tshirtTaxon->expects($this->exactly(2))->method('isEnabled')->willReturn(false);
        $this->tshirtTaxon->expects($this->once())->method('getParent')->willReturn(null);
        $this->tshirtTaxon->expects($this->once())->method('setParent')->with($this->taxon);

        $this->taxon->addChild($this->categoryTaxon);
        $this->taxon->addChild($this->tshirtTaxon);

        $this->assertCount(1, $this->taxon->getEnabledChildren());
        $this->assertContains($this->categoryTaxon, $this->taxon->getEnabledChildren());
    }
}
