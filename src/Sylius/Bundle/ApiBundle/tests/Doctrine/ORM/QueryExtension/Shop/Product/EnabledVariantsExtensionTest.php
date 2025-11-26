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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\HttpOperation;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\EnabledVariantsExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request;

final class EnabledVariantsExtensionTest extends TestCase
{
    private EnabledVariantsExtension $extension;

    /** @var SectionProviderInterface|MockObject */
    private $sectionProvider;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->extension = new EnabledVariantsExtension($this->sectionProvider);
    }

    public function test_it_is_a_query_extension(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->extension);
        $this->assertInstanceOf(QueryItemExtensionInterface::class, $this->extension);
    }

    public function test_it_does_nothing_if_current_resource_is_not_a_product(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->sectionProvider->expects($this->never())->method('getSection');

        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection(
            $queryBuilder,
            $this->createMock(QueryNameGeneratorInterface::class),
            TaxonInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function test_it_does_nothing_if_in_admin_section(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->sectionProvider
            ->expects($this->once())
            ->method('getSection')
            ->willReturn($this->createMock(AdminApiSection::class))
        ;

        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection(
            $queryBuilder,
            $this->createMock(QueryNameGeneratorInterface::class),
            ProductInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    /** @dataProvider getGetOperations */
    public function test_it_filters_out_disabled_variants_on_collection(HttpOperation $operation): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($shopApiSection);

        $queryNameGenerator
            ->expects($this->once())
            ->method('generateJoinAlias')
            ->with('variant')
            ->willReturn('variant')
        ;

        $queryNameGenerator
            ->expects($this->once())
            ->method('generateParameterName')
            ->with('enabled')
            ->willReturn('enabledParameter')
        ;

        $queryBuilder->expects($this->once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilder->expects($this->once())->method('addSelect')->with('variant')->willReturnSelf();
        $queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->with('o.variants', 'variant', 'WITH', 'variant.enabled = :enabledParameter')
            ->willReturnSelf()
        ;

        $queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('enabledParameter', true)
            ->willReturnSelf()
        ;

        $this->extension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ProductInterface::class,
            $operation,
        );
    }

    /** @return iterable<string, HttpOperation> */
    public static function getGetOperations(): iterable
    {
        yield 'Get operation' => [new Get()];
        yield 'GetCollection operation' => [new GetCollection()];
    }
}
