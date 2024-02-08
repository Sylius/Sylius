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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

/** @experimental */
class BlameCart
{
    /**
     * @immutable
     *
     * @var string
     */
    public $shopUserEmail;

    /**
     * @immutable
     *
     * @var string
     */
    public $orderTokenValue;

    public function __construct(string $shopUserEmail, string $orderTokenValue)
    {
        $this->shopUserEmail = $shopUserEmail;
        $this->orderTokenValue = $orderTokenValue;
    }
}
