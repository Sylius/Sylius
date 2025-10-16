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
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Common\EnabledExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Model\ToggleableInterface;

final class EnabledExtensionTest extends TestCase
{
    private EnabledExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->extension = new EnabledExtension($this->sectionProvider);
    }

    public function test_it_does_not_apply_conditions_to_item_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, \stdClass::class, []);
    }

    public function test_it_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(AdminApiSection::class));
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, ToggleableInterface::class, []);
    }

    public function test_it_applies_conditions_to_item(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(ShopApiSection::class));

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $queryNameGenerator->expects($this->once())->method('generateParameterName')->with('enabled')->willReturn('enabled');

        $queryBuilder->expects($this->once())->method('andWhere')->with('o.enabled = :enabled')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setParameter')->with('enabled', true)->willReturnSelf();

        $this->extension->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ToggleableInterface::class,
            [],
            new Get(),
            [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'],
        );
    }

    public function test_it_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(AdminApiSection::class));

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, ToggleableInterface::class);
    }

    public function test_it_applies_conditions_to_collection(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(ShopApiSection::class));

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $queryNameGenerator->expects($this->once())->method('generateParameterName')->with('enabled')->willReturn('enabled');

        $queryBuilder->expects($this->once())->method('andWhere')->with('o.enabled = :enabled')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setParameter')->with('enabled', true)->willReturnSelf();

        $this->extension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ToggleableInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'],
        );
    }
}
