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

namespace Sylius\Bundle\PaymentBundle\Generator;

use Sylius\Component\Payment\Model\PaymentMethodInterface;

final readonly class GatewayConfigNameGenerator implements GatewayConfigNameGeneratorInterface
{
    public function generate(PaymentMethodInterface $paymentMethod): ?string
    {
        $paymentMethodCode = $paymentMethod->getCode();
        if (null === $paymentMethodCode) {
            return null;
        }

        return strtolower(str_replace([' ', '-', '\''], '_', $paymentMethodCode));
    }
}
