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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon\EnabledChildrenExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class EnabledChildrenExtensionTest extends TestCase
{
    /** @var SectionProviderInterface&MockObject */
    private $sectionProvider;

    private EnabledChildrenExtension $extension;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->extension = new EnabledChildrenExtension($this->sectionProvider);
    }

    public function test_does_not_apply_conditions_to_item_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects(self::never())->method('getRootAliases');
        $queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, stdClass::class, []);
    }

    public function test_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $adminApiSection = $this->createMock(AdminApiSection::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn($adminApiSection);

        $queryBuilder->expects(self::never())->method('getRootAliases');
        $queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);
    }

    public function test_applies_extension_to_item_query(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $queryBuilder->expects(self::once())
            ->method('getRootAliases')
            ->willReturn(['rootAlias']);

        $queryNameGenerator->expects(self::once())
            ->method('generateParameterName')
            ->with('enabled')
            ->willReturn('enabled');

        $queryNameGenerator->expects(self::once())
            ->method('generateJoinAlias')
            ->with('child')
            ->willReturn('childAlias');

        $queryBuilder->expects(self::once())
            ->method('addSelect')
            ->with('childAlias')
            ->willReturn($queryBuilder);

        $queryBuilder->expects(self::once())
            ->method('leftJoin')
            ->with('rootAlias.children', 'childAlias', 'WITH', 'childAlias.enabled = :enabled')
            ->willReturn($queryBuilder);

        $queryBuilder->expects(self::once())
            ->method('setParameter')
            ->with('enabled', true)
            ->willReturn($queryBuilder);

        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, TaxonInterface::class, [], null, []);
    }
}
