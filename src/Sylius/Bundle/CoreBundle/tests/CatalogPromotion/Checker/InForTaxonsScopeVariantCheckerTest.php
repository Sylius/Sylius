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

    public function testResolvesConfiguredTaxonsOnlyOncePerTaxonAcrossVariants(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $firstTaxon = $this->createConfiguredTaxon('FIRST_TAXON');
        $secondTaxon = $this->createConfiguredTaxon('SECOND_TAXON');
        $childTaxon = $this->createConfiguredTaxon('CHILD_TAXON');
        $otherTaxon = $this->createConfiguredTaxon('OTHER_TAXON');

        $this->taxonRepository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnCallback(fn (array $criteria) => match ($criteria['code']) {
                'FIRST_TAXON' => $firstTaxon,
                'SECOND_TAXON' => $secondTaxon,
                default => null,
            })
        ;

        $this->taxonTreeRepository
            ->expects($this->exactly(2))
            ->method('children')
            ->willReturnCallback(fn (TaxonInterface $taxon) => $taxon === $secondTaxon ? [$childTaxon] : [])
        ;

        $scope->method('getConfiguration')->willReturn(['taxons' => ['FIRST_TAXON', 'SECOND_TAXON']]);

        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($scope, $this->createVariantWithTaxons([$firstTaxon])));
        $this->assertFalse($this->inForTaxonsScopeVariantChecker->inScope($scope, $this->createVariantWithTaxons([$otherTaxon])));
        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($scope, $this->createVariantWithTaxons([$childTaxon])));
    }

    public function testResolvesTaxonsAgainAfterReset(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $firstTaxon = $this->createConfiguredTaxon('FIRST_TAXON');

        $this->taxonRepository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->with(['code' => 'FIRST_TAXON'])
            ->willReturn($firstTaxon)
        ;

        $this->taxonTreeRepository
            ->expects($this->exactly(2))
            ->method('children')
            ->with($firstTaxon)
            ->willReturn([])
        ;

        $scope->method('getConfiguration')->willReturn(['taxons' => ['FIRST_TAXON']]);

        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($scope, $this->createVariantWithTaxons([$firstTaxon])));
        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($scope, $this->createVariantWithTaxons([$firstTaxon])));

        $this->inForTaxonsScopeVariantChecker->reset();

        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($scope, $this->createVariantWithTaxons([$firstTaxon])));
    }

    public function testThrowsExceptionIfConfiguredTaxonDoesNotExist(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);

        $this->taxonRepository->expects($this->exactly(2))->method('findOneBy')->with(['code' => 'UNKNOWN_TAXON'])->willReturn(null);
        $this->taxonTreeRepository->expects($this->never())->method('children');

        $scope->method('getConfiguration')->willReturn(['taxons' => ['UNKNOWN_TAXON']]);

        $variant = $this->createVariantWithTaxons([]);

        try {
            $this->inForTaxonsScopeVariantChecker->inScope($scope, $variant);
            $this->fail('Expected exception was not thrown.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Taxon with code "UNKNOWN_TAXON" does not exist', $exception->getMessage());
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Taxon with code "UNKNOWN_TAXON" does not exist');

        $this->inForTaxonsScopeVariantChecker->inScope($scope, $variant);
    }

    public function testResolvesTaxonSharedByDifferentScopesOnlyOnce(): void
    {
        $firstScope = $this->createMock(CatalogPromotionScopeInterface::class);
        $secondScope = $this->createMock(CatalogPromotionScopeInterface::class);
        $sharedTaxon = $this->createConfiguredTaxon('SHARED_TAXON');
        $firstTaxon = $this->createConfiguredTaxon('FIRST_TAXON');
        $secondTaxon = $this->createConfiguredTaxon('SECOND_TAXON');

        $findOneByCodes = [];
        $childrenCodes = [];

        $this->taxonRepository
            ->method('findOneBy')
            ->willReturnCallback(function (array $criteria) use (&$findOneByCodes, $sharedTaxon, $firstTaxon, $secondTaxon) {
                $findOneByCodes[] = $criteria['code'];

                return match ($criteria['code']) {
                    'SHARED_TAXON' => $sharedTaxon,
                    'FIRST_TAXON' => $firstTaxon,
                    'SECOND_TAXON' => $secondTaxon,
                    default => null,
                };
            })
        ;

        $this->taxonTreeRepository
            ->method('children')
            ->willReturnCallback(function (TaxonInterface $taxon) use (&$childrenCodes) {
                $childrenCodes[] = $taxon->getCode();

                return [];
            })
        ;

        $firstScope->method('getConfiguration')->willReturn(['taxons' => ['SHARED_TAXON', 'FIRST_TAXON']]);
        $secondScope->method('getConfiguration')->willReturn(['taxons' => ['SHARED_TAXON', 'SECOND_TAXON']]);

        $variant = $this->createVariantWithTaxons([$sharedTaxon]);

        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($firstScope, $variant));
        $this->assertTrue($this->inForTaxonsScopeVariantChecker->inScope($secondScope, $variant));

        $this->assertSame(['SHARED_TAXON', 'FIRST_TAXON', 'SECOND_TAXON'], $findOneByCodes);
        $this->assertSame(['SHARED_TAXON', 'FIRST_TAXON', 'SECOND_TAXON'], $childrenCodes);
    }

    private function createConfiguredTaxon(string $code): MockObject&TaxonInterface
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxon->method('getCode')->willReturn($code);

        return $taxon;
    }

    /** @param array<TaxonInterface> $taxons */
    private function createVariantWithTaxons(array $taxons): MockObject&ProductVariantInterface
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);

        $variant->method('getProduct')->willReturn($product);
        $product->method('getTaxons')->willReturn(new ArrayCollection($taxons));

        return $variant;
    }
}
