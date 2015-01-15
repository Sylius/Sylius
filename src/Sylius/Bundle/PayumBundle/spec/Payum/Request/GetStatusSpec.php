<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\PayumBundle\Payum\Request;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentInterface;

class GetStatusSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \stdClass);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Request\GetStatus');
    }

    function it_extends_base_status_class()
    {
        $this->shouldHaveType('Payum\Core\Request\BaseGetStatus');
    }

    function it_is_new_when_marked_as_new()
    {
        $this->markNew();

        $this->isNew()->shouldReturn(true);
    }

    function it_is_return_new_status_when_marked_as_new()
    {
        $this->markNew();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_NEW);
    }

    function it_is_captured_when_marked_as_captured()
    {
        $this->markCaptured();

        $this->isCaptured()->shouldReturn(true);
    }

    function it_is_return_completed_status_when_marked_as_captured()
    {
        $this->markCaptured();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_COMPLETED);
    }

    function it_is_authorized_when_marked_as_authorized()
    {
        $this->markAuthorized();

        $this->isAuthorized()->shouldReturn(true);
    }

    function it_is_return_authorized_status_when_marked_as_authorized()
    {
        $this->markAuthorized();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_AUTHORIZED);
    }

    function it_is_refunded_when_marked_as_refunded()
    {
        $this->markRefunded();

        $this->isRefunded()->shouldReturn(true);
    }

    function it_is_return_refunded_status_when_marked_as_refunded()
    {
        $this->markRefunded();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_REFUNDED);
    }

    function it_is_pending_when_marked_as_pending()
    {
        $this->markPending();

        $this->isPending()->shouldReturn(true);
    }

    function it_is_return_pending_status_when_marked_as_pending()
    {
        $this->markPending();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_PROCESSING);
    }

    function it_is_failed_when_marked_as_failed()
    {
        $this->markFailed();

        $this->isFailed()->shouldReturn(true);
    }

    function it_is_return_failed_status_when_marked_as_failed()
    {
        $this->markFailed();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_FAILED);
    }

    function it_is_canceled_when_marked_as_canceled()
    {
        $this->markCanceled();

        $this->isCanceled()->shouldReturn(true);
    }

    function it_is_return_void_status_when_marked_as_canceled()
    {
        $this->markCanceled();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_CANCELLED);
    }

    function it_is_suspended_when_marked_as_suspended()
    {
        $this->markSuspended();

        $this->isSuspended()->shouldReturn(true);
    }

    function it_is_return_void_status_when_marked_as_suspended()
    {
        $this->markSuspended();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_PROCESSING);
    }

    function it_is_expired_when_marked_as_expired()
    {
        $this->markExpired();

        $this->isExpired()->shouldReturn(true);
    }

    function it_is_return_failed_status_when_marked_as_expired()
    {
        $this->markExpired();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_FAILED);
    }

    function it_is_unknown_when_marked_as_unknown()
    {
        $this->markUnknown();

        $this->isUnknown()->shouldReturn(true);
    }

    function it_is_return_unknown_status_when_marked_as_unknown()
    {
        $this->markUnknown();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_UNKNOWN);
    }
}
