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

namespace Sylius\Bundle\ApiBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsPaymentConfigurationProvider
{
    public const SERVICE_TAG = 'sylius.api.payment_method_handler';

    public function __construct(
        private int $priority = 0,
    ) {
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
