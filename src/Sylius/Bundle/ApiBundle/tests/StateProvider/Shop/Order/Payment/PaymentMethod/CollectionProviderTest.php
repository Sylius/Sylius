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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Payment\PaymentMethod;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Payment\PaymentMethod\CollectionProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class CollectionProviderTest extends TestCase
{
    private MockObject&PaymentRepositoryInterface $paymentRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&PaymentMethodsResolverInterface $paymentMethodsResolver;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->paymentMethodsResolver = $this->createMock(PaymentMethodsResolverInterface::class);
        $this->collectionProvider = new CollectionProvider(
            $this->paymentRepository,
            $this->orderRepository,
            $this->sectionProvider,
            $this->paymentMethodsResolver,
        );
    }

    public function testProvidesPaymentMethods(): void
    {
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var PaymentMethodInterface|MockObject $methodMock */
        $methodMock = $this->createMock(PaymentMethodInterface::class);

        $operation = new GetCollection(class: PaymentMethodInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValueAndChannel')
            ->with('TOKEN', $channelMock)
            ->willReturn($cartMock);

        $cartMock->expects(self::once())->method('getTokenValue')->willReturn('TOKEN');

        $this->paymentRepository->expects(self::once())
            ->method('findOneByOrderToken')
            ->with(1, 'TOKEN')
            ->willReturn($paymentMock);

        $this->paymentMethodsResolver->expects(self::once())
            ->method('getSupportedMethods')
            ->with($paymentMock)
            ->willReturn([$methodMock]);

        self::assertSame([$methodMock], $this->collectionProvider
            ->provide($operation, ['tokenValue' => 'TOKEN', 'paymentId' => 1], ['sylius_api_channel' => $channelMock]));
    }

    public function testReturnsEmptyArrayIfCartDoesNotExist(): void
    {
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);

        $operation = new GetCollection(class: PaymentMethodInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValueAndChannel')
            ->with('TOKEN', $channelMock)
            ->willReturn(null);

        self::assertSame(
            [],
            $this->collectionProvider->provide(
                $operation,
                ['tokenValue' => 'TOKEN', 'paymentId' => 1],
                ['sylius_api_channel' => $channelMock],
            ),
        );
    }

    public function testReturnsEmptyArrayIfPaymentDoesNotExist(): void
    {
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        $operation = new GetCollection(class: PaymentMethodInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValueAndChannel')
            ->with('TOKEN', $channelMock)
            ->willReturn($cartMock);

        $cartMock->expects(self::once())->method('getTokenValue')->willReturn('TOKEN');

        $this->paymentRepository->expects(self::once())
            ->method('findOneByOrderToken')
            ->with(1, 'TOKEN')
            ->willReturn(null);

        self::assertSame(
            [],
            $this->collectionProvider->provide(
                $operation,
                ['tokenValue' => 'TOKEN', 'paymentId' => 1],
                ['sylius_api_channel' => $channelMock],
            ),
        );
    }

    public function testThrowsAnExceptionWhenResourceIsNotAPaymentMethodInterface(): void
    {
        $operation = new GetCollection(class: \stdClass::class);

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }

    public function testThrowsAnExceptionWhenOperationIsNotGetCollection(): void
    {
        $operation = new Get(class: PaymentMethodInterface::class);

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }

    public function testThrowsAnExceptionWhenOperationIsNotInShopApiSection(): void
    {
        $operation = new GetCollection(class: PaymentMethodInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }

    public function testThrowsAnExceptionWhenUriVariablesDoNotExist(): void
    {
        $operation = new GetCollection(class: PaymentMethodInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }
}
