<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Request;

use Payum\Core\Request\BaseGetStatus;
use Sylius\Component\Payment\Model\PaymentInterface;

class GetStatus extends BaseGetStatus
{
    /**
     * {@inheritdoc}
     */
    public function markNew()
    {
        $this->status = PaymentInterface::STATE_NEW;
    }

    /**
     * {@inheritdoc}
     */
    public function isNew()
    {
        return $this->status === PaymentInterface::STATE_NEW;
    }

    /**
     * {@inheritdoc}
     */
    public function markSuspended()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuspended()
    {
        return $this->status === PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function markExpired()
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return $this->status === PaymentInterface::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function markCanceled()
    {
        $this->status = PaymentInterface::STATE_CANCELLED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCanceled()
    {
        return $this->status === PaymentInterface::STATE_CANCELLED;
    }

    /**
     * {@inheritdoc}
     */
    public function markPending()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function isPending()
    {
        return $this->status === PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function markFailed()
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function isFailed()
    {
        return $this->status === PaymentInterface::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function markUnknown()
    {
        $this->status = PaymentInterface::STATE_UNKNOWN;
    }

    /**
     * {@inheritdoc}
     */
    public function isUnknown()
    {
        return $this->status === PaymentInterface::STATE_UNKNOWN;
    }

    /**
     * {@inheritdoc}
     */
    public function markCaptured()
    {
        $this->status = PaymentInterface::STATE_COMPLETED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCaptured()
    {
        return $this->status === PaymentInterface::STATE_COMPLETED;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthorized()
    {
        return $this->status === PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function markAuthorized()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function isRefunded()
    {
        return $this->status === PaymentInterface::STATE_REFUNDED;
    }

    /**
     * {@inheritdoc}
     */
    public function markRefunded()
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }

    /**
     * {@inheritdoc}
     */
    public function isPayedout()
    {
        return $this->status === PaymentInterface::STATE_REFUNDED;
    }

    /**
     * {@inheritdoc}
     */
    public function markPayedout()
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }
}
