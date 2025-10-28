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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Currency;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Currency\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\CurrencyInterface;

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
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(AdminApiSection::class));
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, CurrencyInterface::class);
    }

    public function test_it_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(AdminApiSection::class));
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, CurrencyInterface::class, []);
    }

    public function test_it_throws_exception_when_collection_context_has_no_channel(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(ShopApiSection::class));
        $this->expectException(\InvalidArgumentException::class);

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, CurrencyInterface::class);
    }

    public function test_it_throws_exception_when_item_context_has_no_channel(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(ShopApiSection::class));
        $this->expectException(\InvalidArgumentException::class);

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, CurrencyInterface::class, []);
    }

    public function test_it_applies_conditions_to_collection_for_shop_api_section(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(Currency::class);
        $baseCurrency = $currency;
        $expr = $this->createMock(Expr::class);
        $exprFunc = $this->createMock(Expr\Func::class);

        $currenciesCollection = new ArrayCollection([$currency]);
        $channel->expects($this->once())->method('getCurrencies')->willReturn($currenciesCollection);
        $channel->expects($this->once())->method('getBaseCurrency')->willReturn($baseCurrency);

        $this->nameGenerator->method('generateParameterName')->with(':currencies')->willReturn(':currencies');
        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->queryBuilder->method('expr')->willReturn($expr);
        $expr->method('in')->with('o.id', ':currencies')->willReturn($exprFunc);

        $this->queryBuilder->expects($this->once())->method('andWhere')->with($exprFunc)->willReturnSelf();

        $this->queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with(
                ':currencies',
                $this->callback(function ($currencies) use ($currency) {
                    return in_array($currency, $currencies, true);
                }),
            )
            ->willReturnSelf();

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->nameGenerator,
            CurrencyInterface::class,
            null,
            [
                ContextKeys::CHANNEL => $channel,
            ],
        );
    }

    public function test_it_applies_conditions_to_item_for_shop_api_section(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(Currency::class);
        $baseCurrency = $currency;
        $expr = $this->createMock(Expr::class);
        $exprFunc = $this->createMock(Expr\Func::class);

        $currenciesCollection = new ArrayCollection([$currency]);
        $channel->expects($this->once())->method('getCurrencies')->willReturn($currenciesCollection);
        $channel->expects($this->once())->method('getBaseCurrency')->willReturn($baseCurrency);

        $this->nameGenerator->method('generateParameterName')->with(':currencies')->willReturn(':currencies');
        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->queryBuilder->method('expr')->willReturn($expr);
        $expr->method('in')->with('o.id', ':currencies')->willReturn($exprFunc);

        $this->queryBuilder->expects($this->once())->method('andWhere')->with($exprFunc)->willReturnSelf();

        $this->queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with(
                ':currencies',
                $this->callback(function ($currencies) use ($currency) {
                    return in_array($currency, $currencies, true);
                }),
            )
            ->willReturnSelf();

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->nameGenerator,
            CurrencyInterface::class,
            [],
            null,
            [
                ContextKeys::CHANNEL => $channel,
            ],
        );
    }
}
