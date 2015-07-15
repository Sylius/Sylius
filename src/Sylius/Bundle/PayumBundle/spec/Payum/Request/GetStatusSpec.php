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
    public function let()
    {
        $this->beConstructedWith(new \stdClass());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Request\GetStatus');
    }

    public function it_extends_base_status_class()
    {
        $this->shouldHaveType('Payum\Core\Request\BaseGetStatus');
    }

    public function it_is_new_when_marked_as_new()
    {
        $this->markNew();

        $this->isNew()->shouldReturn(true);
    }

    public function it_is_return_new_status_when_marked_as_new()
    {
        $this->markNew();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_NEW);
    }

    public function it_is_captured_when_marked_as_captured()
    {
        $this->markCaptured();

        $this->isCaptured()->shouldReturn(true);
    }

    public function it_is_return_completed_status_when_marked_as_captured()
    {
        $this->markCaptured();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_COMPLETED);
    }

    public function it_is_authorized_when_marked_as_authorized()
    {
        $this->markAuthorized();

        $this->isAuthorized()->shouldReturn(true);
    }

    public function it_is_return_authorized_status_when_marked_as_authorized()
    {
        $this->markAuthorized();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_AUTHORIZED);
    }

    public function it_is_refunded_when_marked_as_refunded()
    {
        $this->markRefunded();

        $this->isRefunded()->shouldReturn(true);
    }

    public function it_is_return_refunded_status_when_marked_as_refunded()
    {
        $this->markRefunded();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_REFUNDED);
    }

    public function it_is_pending_when_marked_as_pending()
    {
        $this->markPending();

        $this->isPending()->shouldReturn(true);
    }

    public function it_is_return_pending_status_when_marked_as_pending()
    {
        $this->markPending();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_PROCESSING);
    }

    public function it_is_failed_when_marked_as_failed()
    {
        $this->markFailed();

        $this->isFailed()->shouldReturn(true);
    }

    public function it_is_return_failed_status_when_marked_as_failed()
    {
        $this->markFailed();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_FAILED);
    }

    public function it_is_canceled_when_marked_as_canceled()
    {
        $this->markCanceled();

        $this->isCanceled()->shouldReturn(true);
    }

    public function it_is_return_void_status_when_marked_as_canceled()
    {
        $this->markCanceled();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_CANCELLED);
    }

    public function it_is_suspended_when_marked_as_suspended()
    {
        $this->markSuspended();

        $this->isSuspended()->shouldReturn(true);
    }

    public function it_is_return_void_status_when_marked_as_suspended()
    {
        $this->markSuspended();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_PROCESSING);
    }

    public function it_is_expired_when_marked_as_expired()
    {
        $this->markExpired();

        $this->isExpired()->shouldReturn(true);
    }

    public function it_is_return_failed_status_when_marked_as_expired()
    {
        $this->markExpired();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_FAILED);
    }

    public function it_is_unknown_when_marked_as_unknown()
    {
        $this->markUnknown();

        $this->isUnknown()->shouldReturn(true);
    }

    public function it_is_return_unknown_status_when_marked_as_unknown()
    {
        $this->markUnknown();

        $this->getValue()->shouldReturn(PaymentInterface::STATE_UNKNOWN);
    }
}
