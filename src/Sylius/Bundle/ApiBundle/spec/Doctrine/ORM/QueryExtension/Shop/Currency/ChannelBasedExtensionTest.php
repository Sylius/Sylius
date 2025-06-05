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
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Currency\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\CurrencyInterface;

final class ChannelBasedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private ChannelBasedExtension $channelBasedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->channelBasedExtension = new ChannelBasedExtension($this->sectionProviderMock);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->channelBasedExtension);
        $this->assertInstanceOf(QueryItemExtensionInterface::class, $this->channelBasedExtension);
    }

    public function testDoesNotApplyConditionsToCollectionForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToItemForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->channelBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, []);
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
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, CurrencyInterface::class);
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
        $this->channelBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, CurrencyInterface::class, []);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testThrowsAnExceptionDuringApplyCollectionIfContextHasNoChannel(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $this->expectException(InvalidArgumentException::class);
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, CurrencyInterface::class);
    }

    public function testThrowsAnExceptionDuringApplyItemIfContextHasNoChannel(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $this->expectException(InvalidArgumentException::class);
        $this->channelBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, CurrencyInterface::class, []);
    }

    public function testAppliesConditionsToCollectionForShopApiSection(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var Currency|MockObject $baseCurrencyMock */
        $baseCurrencyMock = $this->createMock(Currency::class);
        /** @var Currency|MockObject $currencyMock */
        $currencyMock = $this->createMock(Currency::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Func|MockObject $exprFuncMock */
        $exprFuncMock = $this->createMock(Func::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $baseCurrencyMock->expects(self::once())->method('__toString')->willReturn('baseCode');
        $currencyMock->expects(self::once())->method('__toString')->willReturn('code');
        $currenciesCollection = new ArrayCollection([$currencyMock]);
        $channelMock->expects(self::once())->method('getCurrencies')->willReturn($currenciesCollection);
        $channelMock->expects(self::once())->method('getBaseCurrency')->willReturn($baseCurrencyMock);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with(':currencies')->willReturn(':currencies');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('in')->with('o.id', ':currencies')->willReturn($exprFuncMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprFuncMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('setParameter')->willReturnMap([[':currencies', [$currencyMock, $baseCurrencyMock], $queryBuilderMock], [':currencies', [$currencyMock, $baseCurrencyMock]]]);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with(':currencies', [$currencyMock, $baseCurrencyMock])->shouldHaveBeenCalledOnce();
    }

    public function testAppliesConditionsToItemForShopApiSection(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var Currency|MockObject $baseCurrencyMock */
        $baseCurrencyMock = $this->createMock(Currency::class);
        /** @var Currency|MockObject $currencyMock */
        $currencyMock = $this->createMock(Currency::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Func|MockObject $exprFuncMock */
        $exprFuncMock = $this->createMock(Func::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $baseCurrencyMock->expects(self::once())->method('__toString')->willReturn('baseCode');
        $currencyMock->expects(self::once())->method('__toString')->willReturn('code');
        $currenciesCollection = new ArrayCollection([$currencyMock]);
        $channelMock->expects(self::once())->method('getCurrencies')->willReturn($currenciesCollection);
        $channelMock->expects(self::once())->method('getBaseCurrency')->willReturn($baseCurrencyMock);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with(':currencies')->willReturn(':currencies');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('in')->with('o.id', ':currencies')->willReturn($exprFuncMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprFuncMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('setParameter')->willReturnMap([[':currencies', [$currencyMock, $baseCurrencyMock], $queryBuilderMock], [':currencies', [$currencyMock, $baseCurrencyMock]]]);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with(':currencies', [$currencyMock, $baseCurrencyMock])->shouldHaveBeenCalledOnce();
    }
}
