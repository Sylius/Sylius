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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

class ChoosePaymentMethod implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface, PaymentMethodCodeAwareInterface, IriToIdentifierConversionAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @immutable
     *
     * @var string|null
     */
    public $paymentId;

    /**
     * @immutable
     *
     * @var string|null
     */
    public $paymentMethodCode;

    public function __construct(string $paymentMethodCode)
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getSubresourceId(): ?string
    {
        return $this->paymentId;
    }

    public function setSubresourceId(?string $subresourceId): void
    {
        $this->paymentId = $subresourceId;
    }

    public function getSubresourceIdAttributeKey(): string
    {
        return 'paymentId';
    }

    public function getPaymentMethodCode(): ?string
    {
        return $this->paymentMethodCode;
    }

    public function setPaymentMethodCode(?string $paymentMethodCode): void
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }
}
