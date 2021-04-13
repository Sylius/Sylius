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

namespace Sylius\Bundle\ApiBundle\Command;

/** @experimental */
class BlameCart
{
    /**
     * @var string
     * @psalm-immutable
     */
    public $shopUserEmail;

    /**
     * @var string
     * @psalm-immutable
     */
    public $cartToken;

    public function __construct(string $shopUserEmail, string $cartToken)
    {
        $this->shopUserEmail = $shopUserEmail;
        $this->cartToken = $cartToken;
    }
}
