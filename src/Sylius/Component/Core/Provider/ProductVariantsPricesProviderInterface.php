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

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface;

trigger_deprecation(
    'sylius/core',
    '1.13',
    'The "%s" class is deprecated, use "%s" instead.',
    ProductVariantsPricesProviderInterface::class,
    ProductVariantsMapProviderInterface::class,
);

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see ProductVariantsMapProviderInterface} instead. */
interface ProductVariantsPricesProviderInterface
{
    public function provideVariantsPrices(ProductInterface $product, ChannelInterface $channel): array;
}
