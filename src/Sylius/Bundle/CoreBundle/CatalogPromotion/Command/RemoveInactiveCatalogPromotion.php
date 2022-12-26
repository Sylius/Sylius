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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Command;

/** @deprecated since 1.13 and will be removed in Sylius 2.0. Use {@see RemoveCatalogPromotion} instead. */
final class RemoveInactiveCatalogPromotion
{
    public function __construct(public string $code)
    {
        trigger_deprecation(
            'sylius/core-bundle',
            '1.13',
            sprintf(
                'The "%s" class is deprecated since Sylius 1.13 and will be removed in 2.0. Use "%s" instead.',
                self::class,
                RemoveCatalogPromotion::class,
            ),
        );
    }
}
