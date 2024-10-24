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

namespace Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Security\Util\Random;

class PaymentSecurityToken implements PaymentSecurityTokenInterface
{
    /** @var string */
    protected $hash;

    /** @var object|null */
    protected $details;

    /** @var string|null */
    protected $afterUrl;

    /** @var string|null */
    protected $targetUrl;

    /** @var string|null */
    protected $gatewayName;

    public function __construct()
    {
        $this->hash = Random::generateToken();
    }

    public function getId(): string
    {
        return $this->hash;
    }

    public function setDetails($details): void
    {
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl($targetUrl): void
    {
        $this->targetUrl = $targetUrl;
    }

    public function getAfterUrl(): ?string
    {
        return $this->afterUrl;
    }

    public function setAfterUrl($afterUrl): void
    {
        $this->afterUrl = $afterUrl;
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function setGatewayName($gatewayName): void
    {
        $this->gatewayName = $gatewayName;
    }
}
