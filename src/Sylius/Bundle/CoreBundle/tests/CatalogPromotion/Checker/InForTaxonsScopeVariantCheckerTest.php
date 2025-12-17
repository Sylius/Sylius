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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class InForTaxonsScopeVariantCheckerTest extends TestCase
{
    private MockObject&TaxonRepositoryInterface $taxonRepository;

    private MockObject&TaxonTreeRepositoryInterface $taxonTreeRepository;

    private InForTaxonsScopeVariantChecker $inForTaxonsScopeVariantChecker;

    protected function setUp(): void
    {
        $this->taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $this->taxonTreeRepository = $this->createMock(TaxonTreeRepositoryInterface::class);
        $this->inForTaxonsScopeVariantChecker = new InForTaxonsScopeVariantChecker($this->taxonRepository, $this->taxonTreeRepository);
    }

    public function testImplementsCatalogPromotionPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(VariantInScopeCheckerInterface::class, $this->inForTaxonsScopeVariantChecker);
    }

    public function testReturnsTrueIfVariantTaxonIsInScopeConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $firstTaxon = $this->createMock(TaxonInterface::class);
        $secondTaxon = $this->createMock(TaxonInterface::class);
        $thirdTaxon = $this->createMock(TaxonInterface::class);
        $fourthTaxon = $this->createMock(TaxonInterface::class);

        $this->taxonRepository
            ->method('findOneBy')
            ->willReturnCallback(fn (array $criteria) => match ($criteria['code']) {
                'FIRST_TAXON' => $firstTaxon,
                'SECOND_TAXON' => $secondTaxon,
                'THIRD_TAXON' => $thirdTaxon,
                'FOURTH_TAXON' => $fourthTaxon,
                default => null,
            });

        $this->taxonTreeRepository->method('children')->willReturn([]);

        $scope->method('getConfiguration')->willReturn(['taxons' => ['FIRST_TAXON', 'SECOND_TAXON']]);

        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([
            $firstTaxon,
            $thirdTaxon,
        ]));

        $firstTaxon->method('getCode')->willReturn('FIRST_TAXON');
        $secondTaxon->method('getCode')->willReturn('SECOND_TAXON');
        $thirdTaxon->method('getCode')->willReturn('THIRD_TAXON');
        $fourthTaxon->method('getCode')->willReturn('FOURTH_TAXON');

        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($scope, $variant));
    }

    public function testReturnsTrueIfVariantTaxonIsAChildOfTaxonInScopeConfiguration(): void
    {
        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $taxonTreeRepository = $this->createMock(TaxonTreeRepositoryInterface::class);
        $checker = new InForTaxonsScopeVariantChecker($taxonRepository, $taxonTreeRepository);

        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $firstTaxon = $this->createMock(TaxonInterface::class);
        $secondTaxon = $this->createMock(TaxonInterface::class);

        $taxonRepository->method('findOneBy')
            ->with(['code' => 'FIRST_TAXON'])
            ->willReturn($firstTaxon);

        $taxonTreeRepository->method('children')
            ->with($firstTaxon)
            ->willReturn([$secondTaxon]);

        $scope->method('getConfiguration')->willReturn(['taxons' => ['FIRST_TAXON']]);

        $variant->method('getProduct')->willReturn($product);
        $product->method('getTaxons')->willReturn(
            new ArrayCollection([$secondTaxon]),
        );

        $firstTaxon->method('getCode')->willReturn('FIRST_TAXON');
        $secondTaxon->method('getCode')->willReturn('SECOND_TAXON');

        $this->assertTrue($checker->inScope($scope, $variant));
    }

    public function testReturnsFalseIfVariantTaxonIsNotInScopeConfiguration(): void
    {
        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $taxonTreeRepository = $this->createMock(TaxonTreeRepositoryInterface::class);
        $checker = new InForTaxonsScopeVariantChecker($taxonRepository, $taxonTreeRepository);

        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $firstTaxon = $this->createMock(TaxonInterface::class);
        $secondTaxon = $this->createMock(TaxonInterface::class);
        $thirdTaxon = $this->createMock(TaxonInterface::class);

        $taxonRepository->method('findOneBy')
            ->willReturnMap([
                [['code' => 'FIRST_TAXON'], $firstTaxon],
                [['code' => 'SECOND_TAXON'], $secondTaxon],
            ])
        ;

        $taxonTreeRepository->method('children')
            ->willReturnCallback(function ($taxon) use ($firstTaxon, $secondTaxon) {
                if ($taxon === $firstTaxon) {
                    return [];
                }
                if ($taxon === $secondTaxon) {
                    return [];
                }

                return [];
            })
        ;

        $scope->method('getConfiguration')->willReturn(['taxons' => ['FIRST_TAXON', 'SECOND_TAXON']]);

        $variant->method('getProduct')->willReturn($product);
        $product->method('getTaxons')->willReturn(
            new ArrayCollection([$thirdTaxon]),
        );

        $firstTaxon->method('getCode')->willReturn('FIRST_TAXON');
        $secondTaxon->method('getCode')->willReturn('SECOND_TAXON');
        $thirdTaxon->method('getCode')->willReturn('THIRD_TAXON');

        $this->assertFalse($checker->inScope($scope, $variant));
    }

    public function testThrowsExceptionIfScopeDoesNotContainsProductConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $scope->expects($this->once())->method('getConfiguration')->willReturn(['FOO' => ['BOO']]);
        $this->expectException(InvalidArgumentException::class);

        $this->inForTaxonsScopeVariantChecker->inScope($scope, $variant);
    }
}
