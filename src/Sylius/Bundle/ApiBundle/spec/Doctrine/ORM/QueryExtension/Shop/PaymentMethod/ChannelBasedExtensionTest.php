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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

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

    public function testFiltersPaymentMethodByCurrentChannel(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('channel')->willReturn('channel');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with(':channel MEMBER OF o.channels')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('channel', $channelMock)->willReturn($queryBuilderMock);
        $this->channelBasedExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            PaymentMethodInterface::class,
            [],
            new Get(),
            [ContextKeys::CHANNEL => $channelMock],
        );
    }

    public function testFiltersPaymentMethodsByCurrentChannel(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('channel')->willReturn('channel');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with(':channel MEMBER OF o.channels')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('channel', $channelMock)->willReturn($queryBuilderMock);
        $this->channelBasedExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            PaymentMethodInterface::class,
            new GetCollection(),
            [ContextKeys::CHANNEL => $channelMock],
        );
    }

    public function testDoesNothingIfTheCurrentResourceIsNotAPaymentMethodForItem(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->channelBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, [], new Get());
    }

    public function testDoesNothingIfTheCurrentResourceIsNotAPaymentMethodForCollection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, new GetCollection());
    }

    public function testDoesNothingIfTheCurrentSectionIsNotAShopForItem(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->channelBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, ShippingMethodInterface::class, [], new Get());
    }

    public function testDoesNothingIfTheCurrentSectionIsNotAShopForCollection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ShippingMethodInterface::class, new GetCollection());
    }
}
