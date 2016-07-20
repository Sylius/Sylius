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
use Sylius\Component\Resource\Model\ResourceInterface;

class PaymentSecurityToken implements
    ResourceInterface,
    TokenInterface
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
    protected $gatewayName;

    public function __construct()
    {
        $this->hash = Random::generateToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * {@inheritdoc}
     *
     * @return IdentityInterface|null
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getAfterUrl()
    {
        return $this->afterUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setAfterUrl($afterUrl)
    {
        $this->afterUrl = $afterUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getGatewayName()
    {
        return $this->gatewayName;
    }

    /**
     * {@inheritdoc}
     */
    public function setGatewayName($gatewayName)
    {
        $this->gatewayName = $gatewayName;
    }
}
