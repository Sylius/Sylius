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

namespace Sylius\Component\Shipping\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ShippingMethodRuleInterface extends ResourceInterface, ConfigurableShippingMethodElementInterface
{
    public function setType(?string $type): void;

    public function setConfiguration(array $configuration): void;

    public function setShippingMethod(?ShippingMethodInterface $shippingMethod): void;
}
