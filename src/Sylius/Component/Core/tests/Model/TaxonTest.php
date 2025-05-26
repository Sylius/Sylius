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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Comparable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonTest extends TestCase
{
    private ImageInterface&MockObject $image;

    private Taxon $taxon;

    protected function setUp(): void
    {
        $this->image = $this->createMock(ImageInterface::class);
        $this->taxon = new Taxon();
    }

    public function testShouldImplementTaxonInterface(): void
    {
        $this->assertInstanceOf(TaxonInterface::class, $this->taxon);
    }

    public function testShouldImplementImagesAwareInterface(): void
    {
        $this->assertInstanceOf(ImagesAwareInterface::class, $this->taxon);
    }

    public function testShouldImplementDoctrineComparable(): void
    {
        $this->assertInstanceOf(Comparable::class, $this->taxon);
    }

    public function testShouldInitializeImageCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->taxon->getImages());
    }

    public function testShouldAddImage(): void
    {
        $this->taxon->addImage($this->image);

        $this->assertTrue($this->taxon->hasImages());
        $this->assertTrue($this->taxon->hasImage($this->image));
    }

    public function testShouldRemoveImage(): void
    {
        $this->taxon->addImage($this->image);

        $this->taxon->removeImage($this->image);

        $this->assertFalse($this->taxon->hasImage($this->image));
    }

    public function testShouldReturnImagesByType(): void
    {
        $this->image->expects($this->once())->method('getType')->willReturn('thumbnail');
        $this->image->expects($this->once())->method('setOwner')->with($this->taxon);

        $this->taxon->addImage($this->image);

        $this->assertEquals(new ArrayCollection([$this->image]), $this->taxon->getImagesByType('thumbnail'));
    }

    public function testShouldBeComparable(): void
    {
        $sameTaxon = new Taxon();
        $sameTaxon->setCode('test');
        $otherTaxon = new Taxon();
        $otherTaxon->setCode('other');
        $this->taxon->setCode('test');

        $this->assertSame(0, $this->taxon->compareTo($sameTaxon));
        $this->assertSame(1, $this->taxon->compareTo($otherTaxon));
    }
}
