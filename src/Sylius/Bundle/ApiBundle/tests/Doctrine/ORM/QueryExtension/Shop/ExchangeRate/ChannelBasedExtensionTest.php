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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ExchangeRate;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ExchangeRate\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRate;
use Sylius\Component\Currency\Model\ExchangeRateInterface;

final class ChannelBasedExtensionTest extends TestCase
{
    private ChannelBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $nameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $this->extension = new ChannelBasedExtension($this->sectionProvider);
    }

    public function test_it_is_initializable(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->extension);
        $this->assertInstanceOf(QueryItemExtensionInterface::class, $this->extension);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, stdClass::class);
    }

    public function test_it_does_not_apply_conditions_to_item_for_unsupported_resource(): void
    {
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, stdClass::class, []);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $adminApiSection = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($adminApiSection);
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, ExchangeRateInterface::class);
    }

    public function test_it_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $adminApiSection = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($adminApiSection);
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, ExchangeRateInterface::class, []);
    }

    public function test_it_throws_an_exception_during_apply_collection_if_context_has_no_channel(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);
        $this->expectException(\InvalidArgumentException::class);

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, ExchangeRate::class);
    }

    public function test_it_throws_an_exception_during_apply_item_if_context_has_no_channel(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);
        $this->expectException(\InvalidArgumentException::class);

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, ExchangeRate::class, []);
    }

    public function test_it_applies_conditions_to_collection_for_shop_api_section(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprOrx = $this->createMock(Expr\Orx::class);
        $exprComparison = $this->createMock(Expr\Comparison::class);

        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);
        $channel->method('getBaseCurrency')->willReturn($currency);
        $this->nameGenerator->method('generateParameterName')->with(':currency')->willReturn(':currency');
        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->queryBuilder->method('expr')->willReturn($expr);

        $eqParameters = [
            'o.sourceCurrency' => $exprComparison,
            'o.targetCurrency' => $exprComparison,
        ];

        $expr->expects($this->exactly(2))
            ->method('eq')
            ->willReturnCallback(function ($field, $value) use ($eqParameters) {
                $this->assertEquals(':currency', $value);
                $this->assertArrayHasKey($field, $eqParameters);

                return $eqParameters[$field];
            });

        $expr->method('orX')->with($exprComparison, $exprComparison)->willReturn($exprOrx);

        $this->queryBuilder->expects($this->once())->method('andWhere')->with($exprOrx)->willReturnSelf();
        $this->queryBuilder->expects($this->once())->method('setParameter')->with(':currency', $currency)->willReturnSelf();

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->nameGenerator,
            ExchangeRateInterface::class,
            null,
            [
                ContextKeys::CHANNEL => $channel,
            ],
        );
    }

    public function test_it_applies_conditions_to_item_for_shop_api_section(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprOrx = $this->createMock(Expr\Orx::class);
        $exprComparison = $this->createMock(Expr\Comparison::class);

        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);
        $channel->method('getBaseCurrency')->willReturn($currency);
        $this->nameGenerator->method('generateParameterName')->with(':currency')->willReturn(':currency');
        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->queryBuilder->method('expr')->willReturn($expr);

        $eqParameters = [
            'o.sourceCurrency' => $exprComparison,
            'o.targetCurrency' => $exprComparison,
        ];

        $expr->expects($this->exactly(2))
            ->method('eq')
            ->willReturnCallback(function ($field, $value) use ($eqParameters) {
                $this->assertEquals(':currency', $value);
                $this->assertArrayHasKey($field, $eqParameters);

                return $eqParameters[$field];
            });

        $expr->method('orX')->with($exprComparison, $exprComparison)->willReturn($exprOrx);

        $this->queryBuilder->expects($this->once())->method('andWhere')->with($exprOrx)->willReturnSelf();
        $this->queryBuilder->expects($this->once())->method('setParameter')->with(':currency', $currency)->willReturnSelf();

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->nameGenerator,
            ExchangeRateInterface::class,
            [],
            null,
            [
                ContextKeys::CHANNEL => $channel,
            ],
        );
    }
}
