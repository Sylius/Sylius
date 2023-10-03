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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Command;

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see RemoveCatalogPromotion} instead. */
final class RemoveInactiveCatalogPromotion
{
    public function __construct(public string $code)
    {
        trigger_deprecation(
            'sylius/core-bundle',
            '1.13',
            'The "%s" class is deprecated and will be removed in Sylius 2.0. Use "%s" instead.',
            self::class,
            RemoveCatalogPromotion::class,
        );
    }
}
