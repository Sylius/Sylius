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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\ItemProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class ItemProviderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&UserContextInterface $userContext;

    private MockObject&PaymentRepositoryInterface $paymentRepository;

    private ItemProvider $itemProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->itemProvider = new ItemProvider($this->sectionProvider, $this->userContext, $this->paymentRepository);
    }

    public function testAStateProvider(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->itemProvider);
    }

    public function testThrowsAnExceptionIfOperationClassIsNotPayment(): void
    {
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);

        $operationMock->expects(self::once())->method('getClass')->willReturn(\stdClass::class);

        self::expectException(\InvalidArgumentException::class);

        $this->itemProvider->provide($operationMock);
    }

    public function testThrowsAnExceptionIfOperationIsNotGet(): void
    {
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);

        $operationMock->expects(self::once())->method('getClass')->willReturn(\stdClass::class);

        self::expectException(\InvalidArgumentException::class);

        $this->itemProvider->provide($operationMock);
    }

    public function testThrowsAnExceptionIfSectionIsNotShopApiSection(): void
    {
        $operation = new Get(class: PaymentInterface::class, name: 'get');

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->itemProvider->provide($operation, [], []);
    }

    public function testReturnsNothingIfUserIsNotShopUser(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);

        $operation = new Get(class: PaymentInterface::class, name: 'get');

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->userContext->expects(self::once())->method('getUser')->willReturn($adminUserMock);

        $this->paymentRepository->expects(self::never())
            ->method('findOneByCustomerAndOrderToken')
            ->with($this->any());

        $this->assertNull($this->itemProvider->provide($operation, [], []));
    }

    public function testReturnsNothingIfCustomerIsNull(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);

        $operation = new Get(class: PaymentInterface::class, name: 'get');

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->userContext->expects(self::once())->method('getUser')->willReturn($shopUserMock);

        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn(null);

        $this->paymentRepository->expects(self::never())->method('findOneByCustomerAndOrderToken')->with($this->any());

        $this->assertNull($this->itemProvider->provide($operation, [], []));
    }

    public function testReturnsPaymentByCustomerAndOrderToken(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);

        $operation = new Get(class: PaymentInterface::class, name: 'get');

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->userContext->expects(self::once())->method('getUser')->willReturn($shopUserMock);

        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);

        $paymentId = 1;

        $this->paymentRepository->expects(self::once())
            ->method('findOneByCustomerAndOrderToken')
            ->with($paymentId, $customerMock, 'token')
            ->willReturn($paymentMock);

        self::assertSame(
            $paymentMock,
            $this->itemProvider->provide($operation, ['paymentId' => $paymentId, 'tokenValue' => 'token'], []),
        );
    }

    public function testReturnsNothingIfPaymentByCustomerAndOrderTokenIsNotFound(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);

        $operation = new Get(class: PaymentInterface::class, name: 'get');

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->userContext->expects(self::once())->method('getUser')->willReturn($shopUserMock);

        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);

        $paymentId = 1;

        $this->paymentRepository->expects(self::once())
            ->method('findOneByCustomerAndOrderToken')
            ->with($paymentId, $customerMock, 'token')
            ->willReturn(null);

        $this->assertNull(
            $this->itemProvider->provide($operation, ['paymentId' => $paymentId, 'tokenValue' => 'token'], []),
        );
    }
}
