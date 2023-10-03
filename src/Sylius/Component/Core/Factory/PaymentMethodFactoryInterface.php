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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of PaymentMethodInterface
 *
 * @extends FactoryInterface<T>
 */
interface PaymentMethodFactoryInterface extends FactoryInterface
{
    public function createWithGateway(string $gatewayFactory): PaymentMethodInterface;
}
