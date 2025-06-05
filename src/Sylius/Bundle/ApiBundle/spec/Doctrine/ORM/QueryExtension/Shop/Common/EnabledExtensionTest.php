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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Common;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Common\EnabledExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Model\ToggleableInterface;

final class EnabledExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private EnabledExtension $enabledExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->enabledExtension = new EnabledExtension($this->sectionProviderMock);
    }

    public function testDoesNotApplyConditionsToItemForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->enabledExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, []);
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
        $this->enabledExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, ToggleableInterface::class, []);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testAppliesConditionsToItem(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('enabled')->willReturn('enabled');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('o.enabled = :enabled')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('enabled', true)->willReturn($queryBuilderMock);
        $this->enabledExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            ToggleableInterface::class,
            [],
            new Get(),
            [ContextKeys::CHANNEL => $channelMock, ContextKeys::LOCALE_CODE => 'en_US'],
        );
    }

    public function testDoesNotApplyConditionsToCollectionForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->enabledExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToCollectionForAdminApiSection(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        $this->enabledExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ToggleableInterface::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testAppliesConditionsToCollection(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('enabled')->willReturn('enabled');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('o.enabled = :enabled')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('enabled', true)->willReturn($queryBuilderMock);
        $this->enabledExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            ToggleableInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channelMock, ContextKeys::LOCALE_CODE => 'en_US'],
        );
    }
}
