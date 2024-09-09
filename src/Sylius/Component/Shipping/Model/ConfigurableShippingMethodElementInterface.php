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

namespace Sylius\Component\Shipping\Model;

use Sylius\Resource\Model\ResourceInterface;

interface ConfigurableShippingMethodElementInterface extends ResourceInterface
{
    public function getType(): ?string;

    public function getConfiguration(): array;

    public function getShippingMethod(): ?ShippingMethodInterface;
}
