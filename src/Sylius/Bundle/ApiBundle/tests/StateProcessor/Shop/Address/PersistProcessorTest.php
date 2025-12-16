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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Shop\Address;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Shop\Address\PersistProcessor;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private MockObject&UserContextInterface $userContext;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->persistProcessor = new PersistProcessor($this->processor, $this->userContext);
    }

    public function testThrowsAnExceptionIfObjectIsNotAnAddress(): void
    {
        $this->processor->expects(self::never())->method('process')->with($this->any());

        $this->userContext->expects(self::never())->method('getUser');

        self::expectException(\InvalidArgumentException::class);

        $this->persistProcessor->process(new \stdClass(), new Post());
    }

    public function testThrowsAnExceptionIfOperationIsNotPost(): void
    {
        $this->processor->expects(self::never())->method('process')->with($this->any());

        $this->userContext->expects(self::never())->method('getUser');

        self::expectException(\InvalidArgumentException::class);

        $this->persistProcessor->process(new \stdClass(), new Delete());
    }

    public function testSetsCustomerAndDefaultAddressIfUserIsShopUser(): void
    {
        /** @var AddressInterface|MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);

        $operation = new Post();

        $this->userContext->expects(self::once())->method('getUser')->willReturn($userMock);

        $userMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);

        $customerMock->expects(self::once())->method('getDefaultAddress')->willReturn(null);

        $addressMock->expects(self::once())->method('setCustomer')->with($customerMock);

        $customerMock->expects(self::once())->method('setDefaultAddress')->with($addressMock);

        $this->processor->expects(self::once())->method('process')->with($addressMock, $operation, [], []);

        $this->persistProcessor->process($addressMock, $operation);
    }

    public function testSetsCustomerAndDefaultAddressIfUserIsShopUserAndCustomerHasDefaultAddress(): void
    {
        /** @var AddressInterface|MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var AddressInterface|MockObject $defaultAddressMock */
        $defaultAddressMock = $this->createMock(AddressInterface::class);

        $operation = new Post();

        $this->userContext->expects(self::once())->method('getUser')->willReturn($userMock);

        $userMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);

        $customerMock->expects(self::once())->method('getDefaultAddress')->willReturn($defaultAddressMock);

        $addressMock->expects(self::once())->method('setCustomer')->with($customerMock);

        $customerMock->expects(self::never())->method('setDefaultAddress');

        $this->processor->expects(self::once())->method('process')->with($addressMock, $operation, [], []);

        $this->persistProcessor->process($addressMock, $operation);
    }

    public function testDoesNotSetCustomerAndDefaultAddressIfUserIsNotShopUser(): void
    {
        /** @var AddressInterface|MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $operation = new Post();

        $this->userContext->expects(self::once())->method('getUser')->willReturn(null);

        $addressMock->expects(self::never())->method('setCustomer');

        $this->processor->expects(self::once())->method('process')->with($addressMock, $operation, [], []);

        $this->persistProcessor->process($addressMock, $operation);
    }
}
