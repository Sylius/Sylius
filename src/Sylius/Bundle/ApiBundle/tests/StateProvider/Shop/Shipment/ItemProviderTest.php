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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Shipment;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Shipment\ItemProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;

final class ItemProviderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&UserContextInterface $userContext;

    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private ItemProvider $itemProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->itemProvider = new ItemProvider($this->sectionProvider, $this->userContext, $this->shipmentRepository);
    }

    public function testAStateProvider(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->itemProvider);
    }

    public function testThrowsAnExceptionIfOperationClassIsNotShipment(): void
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
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        self::expectException(\InvalidArgumentException::class);
        $this->itemProvider->provide($operation, [], []);
    }

    public function testReturnsNothingIfUserIsNotShopUser(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->expects(self::once())->method('getUser')->willReturn($adminUserMock);
        $this->shipmentRepository->expects(self::never())->method('findOneByCustomerAndOrderToken')->with($this->any());
        $this->assertNull($this->itemProvider->provide($operation, [], []));
    }

    public function testReturnsNothingIfCustomerIsNull(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->expects(self::once())->method('getUser')->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn(null);
        $this->shipmentRepository->expects(self::never())->method('findOneByCustomerAndOrderToken')->with($this->any());
        $this->assertNull($this->itemProvider->provide($operation, [], []));
    }

    public function testReturnsShipmentByCustomerAndOrderToken(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->expects(self::once())->method('getUser')->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $shipmentId = 1;
        $this->shipmentRepository->expects(self::once())->method('findOneByCustomerAndOrderToken')->with($shipmentId, $customerMock, 'token')->willReturn($shipmentMock);
        self::assertSame($shipmentMock, $this->itemProvider->provide($operation, ['shipmentId' => $shipmentId, 'tokenValue' => 'token'], []));
    }

    public function testReturnsNothingIfShipmentByCustomerAndOrderTokenIsNotFound(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->expects(self::once())->method('getUser')->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $shipmentId = 1;
        $this->shipmentRepository->expects(self::once())->method('findOneByCustomerAndOrderToken')->with($shipmentId, $customerMock, 'token')->willReturn(null);
        $this->assertNull($this->itemProvider->provide($operation, ['shipmentId' => $shipmentId, 'tokenValue' => 'token'], []));
    }
}
