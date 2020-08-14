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

namespace Sylius\Bundle\ApiBundle\DTO;

use Sylius\Component\Core\Model\ShippingMethodInterface;

class CartShippingMethod implements CartShippingMethodInterface
{
    /** @var string */
    private $code;

    /** @var ShippingMethodInterface */
    private $shippingMethod;

    /** @var int */
    private $cost;

    public function __construct(string $code, ShippingMethodInterface $shippingMethod, int $cost)
    {
        $this->code = $code;
        $this->shippingMethod = $shippingMethod;
        $this->cost = $cost;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getShippingMethod(): ShippingMethodInterface
    {
        return $this->shippingMethod;
    }

    public function getCost(): int
    {
        return $this->cost;
    }
}
