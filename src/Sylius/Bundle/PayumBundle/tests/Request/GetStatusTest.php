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

namespace Tests\Sylius\Bundle\PayumBundle\Request;

use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface;

final class GetStatusTest extends TestCase
{
    private MockObject&TokenInterface $token;

    private GetStatus $getStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->createMock(TokenInterface::class);
        $this->getStatus = new GetStatus($this->token);
    }

    public function testGetStatusRequest(): void
    {
        self::assertInstanceOf(GetStatusInterface::class, $this->getStatus);
    }

    public function testHasUnknownStatusByDefault(): void
    {
        self::assertTrue($this->getStatus->isUnknown());
        self::assertSame(PaymentInterface::STATE_UNKNOWN, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsNew(): void
    {
        $this->getStatus->markNew();

        self::assertTrue($this->getStatus->isNew());
        self::assertSame(PaymentInterface::STATE_NEW, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsSuspended(): void
    {
        $this->getStatus->markSuspended();

        self::assertTrue($this->getStatus->isSuspended());
        self::assertSame(PaymentInterface::STATE_PROCESSING, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsExpired(): void
    {
        $this->getStatus->markExpired();

        self::assertTrue($this->getStatus->isExpired());
        self::assertSame(PaymentInterface::STATE_FAILED, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsCanceled(): void
    {
        $this->getStatus->markCanceled();

        self::assertTrue($this->getStatus->isCanceled());
        self::assertSame(PaymentInterface::STATE_CANCELLED, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsPending(): void
    {
        $this->getStatus->markPending();

        self::assertTrue($this->getStatus->isPending());
        self::assertSame(PaymentInterface::STATE_PROCESSING, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsFailed(): void
    {
        $this->getStatus->markFailed();

        self::assertTrue($this->getStatus->isFailed());
        self::assertSame(PaymentInterface::STATE_FAILED, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsUnknown(): void
    {
        $this->getStatus->markUnknown();

        self::assertTrue($this->getStatus->isUnknown());
        self::assertSame(PaymentInterface::STATE_UNKNOWN, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsCaptured(): void
    {
        $this->getStatus->markCaptured();

        self::assertTrue($this->getStatus->isCaptured());
        self::assertSame(PaymentInterface::STATE_COMPLETED, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsAuthorized(): void
    {
        $this->getStatus->markAuthorized();

        self::assertTrue($this->getStatus->isAuthorized());
        self::assertSame(PaymentInterface::STATE_AUTHORIZED, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsRefunded(): void
    {
        $this->getStatus->markRefunded();

        self::assertTrue($this->getStatus->isRefunded());
        self::assertSame(PaymentInterface::STATE_REFUNDED, $this->getStatus->getValue());
    }

    public function testCanBeMarkedAsPaydout(): void
    {
        $this->getStatus->markPayedout();

        self::assertTrue($this->getStatus->isPayedout());
        self::assertSame(PaymentInterface::STATE_REFUNDED, $this->getStatus->getValue());
    }
}
