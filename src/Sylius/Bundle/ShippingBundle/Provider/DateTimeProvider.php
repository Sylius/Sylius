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

namespace Sylius\Bundle\ShippingBundle\Provider;

trigger_deprecation(
    'sylius/shipping-bundle',
    '1.13',
    'The "%s" interface is deprecated and will be removed in Sylius 2.0. Use "%s" instead.',
    DateTimeProvider::class,
    'Symfony\Component\Clock\Clock',
);

/**
 * @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see 'Symfony\Component\Clock\Clock'} instead.
 */
interface DateTimeProvider
{
    public function today(): \DateTimeInterface;
}
