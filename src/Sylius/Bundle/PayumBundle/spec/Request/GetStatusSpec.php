<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\PayumBundle\Request;

use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface;

final class GetStatusSpec extends ObjectBehavior
{
    function let(TokenInterface $token): void
    {
        $this->beConstructedWith($token);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GetStatus::class);
    }

    function it_is_get_status_request(): void
    {
        $this->shouldImplement(GetStatusInterface::class);
    }

    function it_has_unknown_status_by_default(): void
    {
        $this->isUnknown()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_UNKNOWN);
    }

    function it_can_be_marked_as_new(): void
    {
        $this->markNew();

        $this->isNew()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_NEW);
    }

    function it_can_be_marked_as_suspended(): void
    {
        $this->markSuspended();

        $this->isSuspended()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_PROCESSING);
    }

    function it_can_be_marked_as_expired(): void
    {
        $this->markExpired();

        $this->isExpired()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_FAILED);
    }

    function it_can_be_marked_as_canceled(): void
    {
        $this->markCanceled();

        $this->isCanceled()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_CANCELLED);
    }

    function it_can_be_marked_as_pending(): void
    {
        $this->markPending();

        $this->isPending()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_PROCESSING);
    }

    function it_can_be_marked_as_failed(): void
    {
        $this->markFailed();

        $this->isFailed()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_FAILED);
    }

    function it_can_be_marked_as_unknown(): void
    {
        $this->markUnknown();

        $this->isUnknown()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_UNKNOWN);
    }

    function it_can_be_marked_as_captured(): void
    {
        $this->markCaptured();

        $this->isCaptured()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_COMPLETED);
    }

    function it_can_be_marked_as_authorized(): void
    {
        $this->markAuthorized();

        $this->isAuthorized()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_AUTHORIZED);
    }

    function it_can_be_marked_as_refunded(): void
    {
        $this->markRefunded();

        $this->isRefunded()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_REFUNDED);
    }

    function it_can_be_marked_as_paydout(): void
    {
        $this->markPayedout();

        $this->isPayedout()->shouldReturn(true);
        $this->getValue()->shouldReturn(PaymentInterface::STATE_REFUNDED);
    }
}
