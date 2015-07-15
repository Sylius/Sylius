<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\Random;
use Payum\Core\Storage\IdentityInterface;

class PaymentSecurityToken implements TokenInterface
{
    /**
     * @var string
     */
    protected $hash;

    /**
     * @var IdentityInterface
     */
    protected $details;

    /**
     * @var string
     */
    protected $afterUrl;

    /**
     * @var string
     */
    protected $targetUrl;

    /**
     * @var string
     */
    protected $paymentName;

    public function __construct()
    {
        $this->hash = Random::generateToken();
    }

    /**
     * {@inheritDoc}
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * {@inheritDoc}
     *
     * @return IdentityInterface|null
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritDoc}
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * {@inheritDoc}
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getAfterUrl()
    {
        return $this->afterUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setAfterUrl($afterUrl)
    {
        $this->afterUrl = $afterUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentName()
    {
        return $this->paymentName;
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentName($paymentName)
    {
        $this->paymentName = $paymentName;
    }
}
