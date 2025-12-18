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
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelBasedExtensionTest extends TestCase
{
    private ChannelBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $queryNameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension = new ChannelBasedExtension($this->sectionProvider);
    }

    public function test_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToCollection($this->queryBuilder, $this->queryNameGenerator, \stdClass::class);
    }

    public function test_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $adminApiSection = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($adminApiSection);

        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToCollection($this->queryBuilder, $this->queryNameGenerator, AddressInterface::class);
    }

    public function test_throws_exception_if_context_has_not_channel(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $this->expectException(\InvalidArgumentException::class);

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            TaxonInterface::class,
            new Get(),
        );
    }

    public function test_applies_conditions_for_shop_api_section(): void
    {
        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $menuTaxon = $this->createMock(TaxonInterface::class);
        $firstTaxon = $this->createMock(TaxonInterface::class);
        $secondTaxon = $this->createMock(TaxonInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);
        $channel->method('getMenuTaxon')->willReturn($menuTaxon);
        $menuTaxon->method('getCode')->willReturn('code');
        $this->queryNameGenerator->expects(self::exactly(2))
            ->method('generateParameterName')
            ->with($this->callback(fn ($param) => $param === 'parentCode' || $param === 'enabled'))
            ->willReturnCallback(fn ($param) => $param);
        $this->queryBuilder->expects(self::once())
            ->method('getRootAliases')
            ->willReturn(['o']);
        $this->queryBuilder->expects(self::once())
            ->method('addSelect')
            ->with('child')
            ->willReturnSelf();
        $this->queryBuilder->expects(self::once())
            ->method('innerJoin')
            ->with('o.parent', 'parent')
            ->willReturnSelf();
        $this->queryBuilder->expects(self::once())
            ->method('leftJoin')
            ->with('o.children', 'child', 'WITH', 'child.enabled = true')
            ->willReturnSelf();
        $expectedConditions = [
            'o.enabled = :enabled',
            'parent.code = :parentCode',
        ];
        $callIndex = 0;
        $this->queryBuilder->expects(self::exactly(2))
            ->method('andWhere')
            ->willReturnCallback(function ($condition) use (&$callIndex, $expectedConditions) {
                self::assertEquals($expectedConditions[$callIndex], $condition);
                ++$callIndex;

                return $this->queryBuilder;
            });
        $this->queryBuilder->expects(self::once())
            ->method('addOrderBy')
            ->with('o.position')
            ->willReturnSelf();
        $expectedParams = [
            ['parentCode', 'code'],
            ['enabled', true],
        ];
        $paramIndex = 0;
        $this->queryBuilder->expects(self::exactly(2))
            ->method('setParameter')
            ->willReturnCallback(function ($name, $value) use (&$paramIndex, $expectedParams) {
                self::assertEquals($expectedParams[$paramIndex][0], $name);
                self::assertEquals($expectedParams[$paramIndex][1], $value);
                ++$paramIndex;

                return $this->queryBuilder;
            });
        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            TaxonInterface::class,
            new Get(),
            [
                ContextKeys::CHANNEL => $channel,
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
