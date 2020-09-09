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

/**
 * @experimental
 * @psalm-immutable
 */
class PickupCart
{
    /** @var string|null */
    public $tokenValue;

    public function __construct(?string $tokenValue = null)
    {
        $this->tokenValue = $tokenValue;
    }
}
