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

use Payum\Core\Model\Identity;
use Payum\Core\Security\Util\Random;
use Payum\Core\Storage\IdentityInterface;

class PaymentSecurityToken implements PaymentSecurityTokenInterface
{
    /** @var string */
    protected $hash;

    /** @var array<string, mixed>|null */
    protected ?array $details = null;

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
        if ($details instanceof IdentityInterface) {
            $this->details = ['id' => $details->getId(), 'class' => $details->getClass()];

            return;
        }

        if (is_object($details)) {
            $this->details = (array) $details;

            return;
        }

        $this->details = $details;
    }

    /** @return IdentityInterface|array<string, mixed>|null */
    public function getDetails(): array|IdentityInterface|null
    {
        if (is_array($this->details) && isset($this->details['id'], $this->details['class'])) {
            return new Identity($this->details['id'], $this->details['class']);
        }

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
