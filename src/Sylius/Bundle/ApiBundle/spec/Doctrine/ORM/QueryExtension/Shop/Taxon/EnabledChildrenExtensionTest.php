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
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private EnabledChildrenExtension $enabledChildrenExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->enabledChildrenExtension = new EnabledChildrenExtension($this->sectionProviderMock);
    }

    public function testDoesNotApplyConditionsToItemForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->enabledChildrenExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, []);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToItemForAdminApiSection(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        $this->enabledChildrenExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, AddressInterface::class, []);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testAppliesExtensionToItemQuery(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['rootAlias']);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('enabled')->willReturn('enabled');
        $queryNameGeneratorMock->expects(self::once())->method('generateJoinAlias')->with('child')->willReturn('childAlias');
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('childAlias')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('leftJoin')->with('rootAlias.children', 'childAlias', 'WITH', 'childAlias.enabled = :enabled')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('enabled', true)->willReturn($queryBuilderMock);
        $this->enabledChildrenExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, TaxonInterface::class, [], null, []);
    }
}
