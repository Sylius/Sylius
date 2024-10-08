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

namespace Sylius\Bundle\PaymentBundle\Tests\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\EventListener\PaymentMethodChangeEventListener;
use Sylius\Component\Payment\Canceller\PaymentRequestCancellerInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentMethodChangeEventListenerTest extends TestCase
{
    /** @test */
    public function it_updates_payment_request_when_the_payment_has_changed_payment_method(): void
    {
        $paymentRequestCanceller = $this->createMock(PaymentRequestCancellerInterface::class);
        $payment = $this->createMock(PaymentInterface::class);
        $oldMethod = $this->createMock(PaymentMethodInterface::class);
        $newMethod = $this->createMock(PaymentMethodInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $payment->expects($this->once())->method('getId')->willReturn(1);

        $newMethod->expects($this->once())
            ->method('getCode')
            ->willReturn('newMethodCode');

        $unitOfWork->expects($this->once())
            ->method('getEntityChangeSet')
            ->with($payment)
            ->willReturn(['method' => [$oldMethod, $newMethod]]);

        $entityManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $args = new PostUpdateEventArgs($payment, $entityManager);

        $paymentRequestCanceller->expects($this->once())
            ->method('cancelPaymentRequests')
            ->with(1, 'newMethodCode');

        $listener = new PaymentMethodChangeEventListener($paymentRequestCanceller);
        $listener->postUpdate($args);
    }

    /** @test */
    public function it_does_not_update_if_entity_is_not_payment(): void
    {
        $paymentRequestCanceller = $this->createMock(PaymentRequestCancellerInterface::class);
        $nonPaymentEntity = new \stdClass();
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $args = new PostUpdateEventArgs($nonPaymentEntity, $entityManager);

        $paymentRequestCanceller->expects($this->never())
            ->method('cancelPaymentRequests');

        $listener = new PaymentMethodChangeEventListener($paymentRequestCanceller);
        $listener->postUpdate($args);
    }

    /** @test */
    public function it_does_not_update_if_payment_method_did_not_change(): void
    {
        $paymentRequestCanceller = $this->createMock(PaymentRequestCancellerInterface::class);
        $payment = $this->createMock(PaymentInterface::class);
        $oldMethod = $this->createMock(PaymentMethodInterface::class);
        $newMethod = $oldMethod;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $unitOfWork->expects($this->once())
            ->method('getEntityChangeSet')
            ->with($payment)
            ->willReturn(['method' => [$oldMethod, $newMethod]]);

        $entityManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $args = new PostUpdateEventArgs($payment, $entityManager);

        $paymentRequestCanceller->expects($this->never())
            ->method('cancelPaymentRequests');

        $listener = new PaymentMethodChangeEventListener($paymentRequestCanceller);
        $listener->postUpdate($args);
    }
}
