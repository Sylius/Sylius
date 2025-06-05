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

namespace Tests\Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\TaxonsToCodesTransformer;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class TaxonsToCodesTransformerTest extends TestCase
{
    private MockObject&TaxonRepositoryInterface $taxonRepository;

    private TaxonsToCodesTransformer $transformer;

    protected function setUp(): void
    {
        $this->taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $this->transformer = new TaxonsToCodesTransformer($this->taxonRepository);
    }

    public function testImplementsDataTransformerInterface(): void
    {
        $this->assertInstanceOf(DataTransformerInterface::class, $this->transformer);
    }

    public function testTransformsArrayOfTaxonsCodesToTaxonsCollection(): void
    {
        $bows = $this->createMock(TaxonInterface::class);
        $swords = $this->createMock(TaxonInterface::class);

        $this->taxonRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['code' => ['bows', 'swords']])
            ->willReturn([$bows, $swords])
        ;

        $result = $this->transformer->transform(['bows', 'swords']);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([$bows, $swords], $result->toArray());
    }

    public function testTransformsOnlyExistingTaxons(): void
    {
        $bows = $this->createMock(TaxonInterface::class);

        $this->taxonRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['code' => ['bows', 'swords']])
            ->willReturn([$bows])
        ;

        $result = $this->transformer->transform(['bows', 'swords']);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([$bows], $result->toArray());
    }

    public function testTransformsEmptyArrayIntoEmptyCollection(): void
    {
        $result = $this->transformer->transform([]);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function testThrowsExceptionIfValueToTransformIsNotArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transformer->transform('badObject');
    }

    public function testReverseTransformsIntoArrayOfTaxonsCodes(): void
    {
        $axes = $this->createMock(TaxonInterface::class);
        $shields = $this->createMock(TaxonInterface::class);

        $axes->method('getCode')->willReturn('axes');
        $shields->method('getCode')->willReturn('shields');

        $collection = new ArrayCollection([$axes, $shields]);
        $result = $this->transformer->reverseTransform($collection);

        $this->assertSame(['axes', 'shields'], $result);
    }

    public function testThrowsExceptionIfReverseTransformedObjectIsNotCollection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transformer->reverseTransform('badObject');
    }

    public function testReturnsEmptyArrayIfPassedCollectionIsEmpty(): void
    {
        $collection = new ArrayCollection();
        $result = $this->transformer->reverseTransform($collection);

        $this->assertSame([], $result);
    }
}
