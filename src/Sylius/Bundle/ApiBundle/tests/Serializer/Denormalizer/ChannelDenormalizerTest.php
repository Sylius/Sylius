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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelDenormalizer;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ChannelDenormalizerTest extends TestCase
{
    private FactoryInterface&MockObject $configFactory;

    private FactoryInterface&MockObject $shopBillingDataFactory;

    private ChannelDenormalizer $channelDenormalizer;

    private const ALREADY_CALLED = 'sylius_channel_denormalizer_already_called';

    protected function setUp(): void
    {
        parent::setUp();
        $this->configFactory = $this->createMock(FactoryInterface::class);
        $this->shopBillingDataFactory = $this->createMock(FactoryInterface::class);
        $this->channelDenormalizer = new ChannelDenormalizer($this->configFactory, $this->shopBillingDataFactory);
    }

    public function testDoesNotSupportDenormalizationWhenTheDenormalizerHasAlreadyBeenCalled(): void
    {
        self::assertFalse(
            $this->channelDenormalizer->supportsDenormalization(
                [],
                ChannelInterface::class,
                context: [self::ALREADY_CALLED => true],
            ),
        );
    }

    public function testDoesNotSupportDenormalizationWhenDataIsNotAnArray(): void
    {
        self::assertFalse(
            $this->channelDenormalizer->supportsDenormalization('string', ChannelInterface::class),
        );
    }

    public function testDoesNotSupportDenormalizationWhenTypeIsNotAChannel(): void
    {
        self::assertFalse($this->channelDenormalizer->supportsDenormalization([], 'string'));
    }

    public function testThrowsAnExceptionWhenDenormalizingAnObjectThatIsNotAChannel(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $this->channelDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with([], 'string', null, [self::ALREADY_CALLED => true])
            ->willReturn(new \stdClass());

        self::expectException(\InvalidArgumentException::class);

        $this->channelDenormalizer->denormalize([], 'string');
    }

    public function testReturnsChannelAsIsWhenShopBillingDataAndChannelPriceHistoryConfigHasAlreadyBeenSet(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ShopBillingDataInterface|MockObject $shopBillingDataMock */
        $shopBillingDataMock = $this->createMock(ShopBillingDataInterface::class);
        /** @var ChannelPriceHistoryConfigInterface|MockObject $configMock */
        $configMock = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);

        $this->channelDenormalizer->setDenormalizer($denormalizerMock);

        $channelMock->expects(self::once())->method('getChannelPriceHistoryConfig')->willReturn($configMock);

        $channelMock->expects(self::once())->method('getShopBillingData')->willReturn($shopBillingDataMock);

        $channelMock->expects(self::never())->method('setChannelPriceHistoryConfig');

        $this->configFactory->expects(self::never())->method('createNew');

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with([], ChannelInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($channelMock);

        self::assertSame($channelMock, $this->channelDenormalizer->denormalize([], ChannelInterface::class));
    }

    public function testAddsANewChannelPriceHistoryConfigWhenChannelHasNone(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ShopBillingDataInterface|MockObject $shopBillingDataMock */
        $shopBillingDataMock = $this->createMock(ShopBillingDataInterface::class);
        /** @var ChannelPriceHistoryConfigInterface|MockObject $configMock */
        $configMock = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);

        $this->channelDenormalizer->setDenormalizer($denormalizerMock);

        $channelMock->expects(self::once())->method('getChannelPriceHistoryConfig')->willReturn(null);

        $channelMock->expects(self::once())->method('getShopBillingData')->willReturn($shopBillingDataMock);

        $this->configFactory->expects(self::once())->method('createNew')->willReturn($configMock);

        $channelMock->expects(self::once())->method('setChannelPriceHistoryConfig')->with($configMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with([], ChannelInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($channelMock);

        self::assertSame($channelMock, $this->channelDenormalizer->denormalize([], ChannelInterface::class));
    }

    public function testAddsANewShopBillingDataWhenChannelHasNone(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ShopBillingDataInterface|MockObject $shopBillingDataMock */
        $shopBillingDataMock = $this->createMock(ShopBillingDataInterface::class);
        /** @var ChannelPriceHistoryConfigInterface|MockObject $configMock */
        $configMock = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);

        $this->channelDenormalizer->setDenormalizer($denormalizerMock);

        $channelMock->expects(self::once())->method('getChannelPriceHistoryConfig')->willReturn($configMock);

        $channelMock->expects(self::once())->method('getShopBillingData')->willReturn(null);

        $this->shopBillingDataFactory->expects(self::once())->method('createNew')->willReturn($shopBillingDataMock);

        $channelMock->expects(self::once())->method('setShopBillingData')->with($shopBillingDataMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with([], ChannelInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($channelMock);

        self::assertSame($channelMock, $this->channelDenormalizer->denormalize([], ChannelInterface::class));
    }
}
