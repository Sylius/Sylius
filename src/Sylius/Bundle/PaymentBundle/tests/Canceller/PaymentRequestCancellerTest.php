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

namespace Tests\Sylius\Bundle\PaymentBundle\Canceller;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Canceller\PaymentRequestCanceller;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PaymentRequestCancellerTest extends TestCase
{
    private const STATE_MACHINE_GRAPH = 'sylius_payment_request';

    private const CANCEL_TRANSITION = 'cancel';

    private MockObject&PaymentRequestRepositoryInterface $paymentRequestRepository;

    private MockObject&StateMachineInterface $stateMachine;

    private MockObject&ObjectManager $objectManager;

    private PaymentRequestCanceller $paymentRequestCanceller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentRequestRepository = $this->createMock(PaymentRequestRepositoryInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->paymentRequestCanceller = new PaymentRequestCanceller(
            $this->paymentRequestRepository,
            $this->stateMachine,
            $this->objectManager,
            [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING],
        );
    }

    public function testCancelsPaymentRequestsIfThePaymentMethodCodeIsDifferent(): void
    {
        /** @var PaymentRequestInterface&MockObject $paymentRequest1 */
        $paymentRequest1 = $this->createMock(PaymentRequestInterface::class);
        /** @var PaymentRequestInterface&MockObject $paymentRequest2 */
        $paymentRequest2 = $this->createMock(PaymentRequestInterface::class);
        /** @var PaymentMethodInterface&MockObject $paymentMethod1 */
        $paymentMethod1 = $this->createMock(PaymentMethodInterface::class);
        /** @var PaymentMethodInterface&MockObject $paymentMethod2 */
        $paymentMethod2 = $this->createMock(PaymentMethodInterface::class);

        $this->paymentRequestRepository
            ->expects(self::once())
            ->method('findByPaymentIdAndStates')
            ->with(1, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING])
            ->willReturn([$paymentRequest1, $paymentRequest2]);

        $paymentRequest1->expects(self::once())
            ->method('getMethod')
            ->willReturn($paymentMethod1);

        $paymentMethod1->expects(self::once())
            ->method('getCode')
            ->willReturn('payment_method_with_different_code');

        $paymentRequest2->expects(self::once())
            ->method('getMethod')
            ->willReturn($paymentMethod2);

        $paymentMethod2->expects(self::once())
            ->method('getCode')
            ->willReturn('payment_method_code');

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with(
                $paymentRequest1,
                self::STATE_MACHINE_GRAPH,
                self::CANCEL_TRANSITION,
                [],
            );

        $this->objectManager->expects(self::once())
            ->method('persist')
            ->with(self::callback(fn ($object) => $object === $paymentRequest1));

        $this->objectManager->expects(self::once())
            ->method('flush');

        $this->paymentRequestCanceller->cancelPaymentRequests(1, 'payment_method_code');
    }

    public function testDoesNotCancelPaymentRequestsIfNoneFound(): void
    {
        $this->paymentRequestRepository->expects(self::once())
            ->method('findByPaymentIdAndStates')
            ->with(1, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING])
            ->willReturn([]);

        $this->stateMachine->expects(self::never())->method('apply');

        $this->paymentRequestCanceller->cancelPaymentRequests(1, 'payment_method_code');
    }
}
