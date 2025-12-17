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

namespace Tests\Sylius\Bundle\TaxonomyBundle\Provider;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\TaxonomyBundle\Provider\TaxonTreeProvider;
use Sylius\Component\Taxonomy\Exception\TaxonNotFoundException;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

#[CoversClass(TaxonTreeProvider::class)]
final class TaxonTreeProviderTest extends TestCase
{
    private TaxonRepositoryInterface $taxonRepository;

    private NestedTreeRepository $taxonTreeRepository;

    private TaxonTreeProvider $provider;

    protected function setUp(): void
    {
        $this->taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $this->taxonTreeRepository = $this->createMock(NestedTreeRepository::class);
        $this->provider = new TaxonTreeProvider($this->taxonRepository, $this->taxonTreeRepository);
    }

    #[Test]
    public function it_throws_exception_if_taxon_not_found_when_getting_path(): void
    {
        $this->taxonRepository
            ->method('findOneBy')
            ->with(['code' => 'non_existing_code'])
            ->willReturn(null)
        ;

        $this->expectException(TaxonNotFoundException::class);
        $this->expectExceptionMessage('Taxon with code "non_existing_code" could not be found.');

        $this->provider->getPathTo('non_existing_code');
    }

    #[Test]
    public function it_throws_exception_if_taxon_not_found_when_getting_branch(): void
    {
        $this->taxonRepository
            ->method('findOneBy')
            ->with(['code' => 'non_existing_code'])
            ->willReturn(null)
        ;

        $this->expectException(TaxonNotFoundException::class);
        $this->expectExceptionMessage('Taxon with code "non_existing_code" could not be found.');

        $this->provider->getBranchWith('non_existing_code');
    }

    #[Test]
    #[DataProvider('getPathCases')]
    public function it_returns_path_to_taxon_correctly_based_on_include_root_flag(
        string $taxonCode,
        bool $includeRoot,
        array $inputTaxons,
        array $expected,
    ): void {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxons = [];

        foreach ($inputTaxons as $code => $isRoot) {
            $mock = $this->createMock(TaxonInterface::class);
            $mock->method('isRoot')->willReturn($isRoot);
            $taxons[$code] = $mock;
        }

        $this->taxonRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => $taxonCode])
            ->willReturn($taxon)
        ;
        $this->taxonTreeRepository
            ->expects(self::once())
            ->method('getPath')
            ->with($taxon)
            ->willReturn($taxons)
        ;

        $result = $this->provider->getPathTo($taxonCode, $includeRoot);

        $expectedTaxons = [];
        foreach ($taxons as $code => $taxon) {
            if (in_array($code, $expected, true)) {
                $expectedTaxons[$code] = $taxon;
            }
        }

        $this->assertSame(array_values($expectedTaxons), array_values($result));
    }

    #[Test]
    #[DataProvider('getBranchCases')]
    public function it_returns_branch_with_path_and_children_correctly_based_on_include_root_flag(
        string $taxonCode,
        bool $includeRoot,
        array $inputTaxons,
        array $expected,
    ): void {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxons = [];

        foreach ($inputTaxons as $code => $isRoot) {
            $mock = $this->createMock(TaxonInterface::class);
            $mock->method('isRoot')->willReturn($isRoot);
            $taxons[$code] = $mock;
        }

        $this->taxonRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => $taxonCode])
            ->willReturn($taxon)
        ;
        $this->taxonTreeRepository
            ->expects(self::once())
            ->method('getPath')
            ->with($taxon)
            ->willReturn(array_slice($taxons, 0, 2))
        ;
        $this->taxonTreeRepository
            ->expects(self::once())
            ->method('getChildren')
            ->with($taxon)
            ->willReturn([end($taxons)])
        ;

        $result = $this->provider->getBranchWith($taxonCode, $includeRoot);

        $expectedTaxons = [];
        foreach ($taxons as $code => $taxon) {
            if (in_array($code, $expected, true)) {
                $expectedTaxons[$code] = $taxon;
            }
        }

        $this->assertSame(array_values($expectedTaxons), array_values($result));
    }

    public static function getPathCases(): iterable
    {
        yield 'excluding root' => [
            'not_root',
            false,
            ['root' => true, 'not_root' => false],
            ['not_root'],
        ];

        yield 'including root' => [
            'not_root',
            true,
            ['root' => true, 'not_root' => false],
            ['root', 'not_root'],
        ];
    }

    public static function getBranchCases(): iterable
    {
        yield 'excluding root' => [
            'mid',
            false,
            ['root' => true, 'mid' => false, 'child' => false],
            ['mid', 'child'],
        ];

        yield 'including root' => [
            'mid',
            true,
            ['root' => true, 'mid' => false, 'child' => false],
            ['root', 'mid', 'child'],
        ];
    }
}
