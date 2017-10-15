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

namespace Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\Random;
use Sylius\Component\Resource\Model\ResourceInterface;

class PaymentSecurityToken implements ResourceInterface, TokenInterface
{
    /**
     * @var string
     */
    protected $hash;

    /**
     * @var mixed
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
    public function getId(): string
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function setDetails($details): void
    {
        $this->details = $details;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetUrl($targetUrl): void
    {
        $this->targetUrl = $targetUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getAfterUrl(): ?string
    {
        return $this->afterUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setAfterUrl($afterUrl): void
    {
        $this->afterUrl = $afterUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    /**
     * {@inheritdoc}
     */
    public function setGatewayName($gatewayName): void
    {
        $this->gatewayName = $gatewayName;
    }
}
