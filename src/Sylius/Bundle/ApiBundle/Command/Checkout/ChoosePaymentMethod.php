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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

class ChoosePaymentMethod implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @psalm-immutable
     * @var string|int
     */
    public $paymentIdentifier;

    /**
     * @psalm-immutable
     * @var string
     */
    public $paymentMethod;

    /**
     * @param int|string $paymentIdentifier
     */
    public function __construct($paymentIdentifier, string $paymentMethod)
    {
        $this->paymentIdentifier = $paymentIdentifier;
        $this->paymentMethod = $paymentMethod;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }
}
