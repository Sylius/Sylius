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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class ApplyCouponToCart implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /** @var string|null */
    public $couponCode;

    public function __construct(?string $couponCode)
    {
        $this->couponCode = $couponCode;
    }

    public static function createFromData(string $orderTokenValue, ?string $couponCode): self
    {
        $command = new self($couponCode);

        $command->setOrderTokenValue($orderTokenValue);

        return $command;
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
