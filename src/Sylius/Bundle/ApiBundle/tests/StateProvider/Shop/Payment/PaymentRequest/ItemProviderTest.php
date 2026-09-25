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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\PaymentRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProviderInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\PaymentRequest\ItemProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

#[AllowMockObjectsWithoutExpectations]
final class ItemProviderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&UserContextInterface $userContext;

    private MockObject&PaymentRequestRepositoryInterface $paymentRequestRepository;

    private FinalizedPaymentRequestCheckerInterface&MockObject $finalizedPaymentRequestChecker;

    private ItemProvider $itemProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->paymentRequestRepository = $this->createMock(PaymentRequestRepositoryInterface::class);
        $this->finalizedPaymentRequestChecker = $this->createMock(FinalizedPaymentRequestCheckerInterface::class);
        $this->itemProvider = new ItemProvider(
            $this->sectionProvider,
            $this->paymentRequestRepository,
            $this->finalizedPaymentRequestChecker,
            $this->userContext,
        );
    }

    public function testAStateProvider(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->itemProvider);
    }

    public function testThrowsAnExceptionIfOperationClassIsNotPaymentRequest(): void
    {
        /** @var Operation&MockObject $operation */
        $operation = $this->createMock(Operation::class);
        $operation->expects(self::once())->method('getClass')->willReturn(\stdClass::class);

        self::expectException(\InvalidArgumentException::class);

        $this->itemProvider->provide($operation);
    }

    public function testThrowsAnExceptionIfOperationIsNotPut(): void
    {
        /** @var Operation&MockObject $operation */
        $operation = $this->createMock(Operation::class);
        $operation->expects(self::once())->method('getClass')->willReturn(PaymentRequestInterface::class);

        self::expectException(\InvalidArgumentException::class);

        $this->itemProvider->provide($operation);
    }

    public function testThrowsAnExceptionIfSectionIsNotShopApiSection(): void
    {
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->itemProvider->provide($this->putOperation(), [], []);
    }

    public function testReturnsNothingIfPaymentRequestIsNotFound(): void
    {
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn(null);
        $this->userContext->expects(self::never())->method('getUser');
        $this->finalizedPaymentRequestChecker->expects(self::never())->method('isFinal');

        self::assertNull($this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    public function testReturnsNothingIfShopUserHasNoCustomer(): void
    {
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        $user->expects(self::once())->method('getCustomer')->willReturn(null);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->expects(self::once())->method('getUser')->willReturn($user);
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn(
            $this->createMock(PaymentRequestInterface::class),
        );
        $this->finalizedPaymentRequestChecker->expects(self::never())->method('isFinal');

        self::assertNull($this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    public function testReturnsNothingIfPaymentRequestIsNotOwnedByTheCustomer(): void
    {
        $customer = $this->stubAuthenticatedCustomer();

        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomer')->willReturn($this->createMock(CustomerInterface::class));

        /** @var PaymentInterface&MockObject $payment */
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn($order);

        /** @var PaymentRequestInterface&MockObject $paymentRequest */
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);
        $paymentRequest->method('getPayment')->willReturn($payment);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn($paymentRequest);
        $this->finalizedPaymentRequestChecker->expects(self::never())->method('isFinal');

        self::assertNull($this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
        self::assertNotSame($customer, $order->getCustomer());
    }

    public function testReturnsNothingIfPaymentRequestIsOwnedByTheCustomerButInFinalState(): void
    {
        $paymentRequest = $this->createOwnedPaymentRequest($this->stubAuthenticatedCustomer());

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn($paymentRequest);
        $this->finalizedPaymentRequestChecker->expects(self::once())->method('isFinal')->with($paymentRequest)->willReturn(true);

        self::assertNull($this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    public function testReturnsThePaymentRequestWhenOwnedByTheCustomerAndNotFinal(): void
    {
        $paymentRequest = $this->createOwnedPaymentRequest($this->stubAuthenticatedCustomer());

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn($paymentRequest);
        $this->finalizedPaymentRequestChecker->expects(self::once())->method('isFinal')->with($paymentRequest)->willReturn(false);

        self::assertSame($paymentRequest, $this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    public function testWithoutUserContextSkipsOwnershipCheck(): void
    {
        $itemProvider = new ItemProvider(
            $this->sectionProvider,
            $this->paymentRequestRepository,
            $this->finalizedPaymentRequestChecker,
        );

        /** @var PaymentRequestInterface&MockObject $paymentRequest */
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->expects(self::never())->method('getUser');
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn($paymentRequest);
        $this->finalizedPaymentRequestChecker->expects(self::once())->method('isFinal')->with($paymentRequest)->willReturn(false);

        self::assertSame($paymentRequest, $itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    public function testReturnsThePaymentRequestForAnonymousUserWhenOrderHasNoCustomer(): void
    {
        $this->userContext->expects(self::once())->method('getUser')->willReturn(null);

        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomer')->willReturn(null);

        /** @var PaymentInterface&MockObject $payment */
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn($order);

        /** @var PaymentRequestInterface&MockObject $paymentRequest */
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);
        $paymentRequest->method('getPayment')->willReturn($payment);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn($paymentRequest);
        $this->finalizedPaymentRequestChecker->expects(self::once())->method('isFinal')->with($paymentRequest)->willReturn(false);

        self::assertSame($paymentRequest, $this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    public function testReturnsThePaymentRequestForAnonymousUserWhenOrderWasCreatedByGuest(): void
    {
        $this->userContext->expects(self::once())->method('getUser')->willReturn(null);

        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getUser')->willReturn($this->createMock(ShopUserInterface::class));

        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomer')->willReturn($customer);
        $order->method('isCreatedByGuest')->willReturn(true);

        /** @var PaymentInterface&MockObject $payment */
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn($order);

        /** @var PaymentRequestInterface&MockObject $paymentRequest */
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);
        $paymentRequest->method('getPayment')->willReturn($payment);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn($paymentRequest);
        $this->finalizedPaymentRequestChecker->expects(self::once())->method('isFinal')->with($paymentRequest)->willReturn(false);

        self::assertSame($paymentRequest, $this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    public function testReturnsNothingForAnonymousUserWhenOrderHasRegisteredCustomerAndNotCreatedByGuest(): void
    {
        $this->userContext->expects(self::once())->method('getUser')->willReturn(null);

        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getUser')->willReturn($this->createMock(ShopUserInterface::class));

        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomer')->willReturn($customer);
        $order->method('isCreatedByGuest')->willReturn(false);

        /** @var PaymentInterface&MockObject $payment */
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn($order);

        /** @var PaymentRequestInterface&MockObject $paymentRequest */
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);
        $paymentRequest->method('getPayment')->willReturn($payment);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->paymentRequestRepository->expects(self::once())->method('find')->with('hash')->willReturn($paymentRequest);
        $this->finalizedPaymentRequestChecker->expects(self::never())->method('isFinal');

        self::assertNull($this->itemProvider->provide($this->putOperation(), ['hash' => 'hash'], []));
    }

    private function putOperation(): Put
    {
        return new Put(class: PaymentRequestInterface::class, name: 'put');
    }

    private function stubAuthenticatedCustomer(): CustomerInterface&MockObject
    {
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);

        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        $user->method('getCustomer')->willReturn($customer);

        $this->userContext->expects(self::once())->method('getUser')->willReturn($user);

        return $customer;
    }

    private function createOwnedPaymentRequest(CustomerInterface $customer): MockObject&PaymentRequestInterface
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomer')->willReturn($customer);

        /** @var PaymentInterface&MockObject $payment */
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn($order);

        /** @var PaymentRequestInterface&MockObject $paymentRequest */
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);
        $paymentRequest->method('getPayment')->willReturn($payment);

        return $paymentRequest;
    }
}
