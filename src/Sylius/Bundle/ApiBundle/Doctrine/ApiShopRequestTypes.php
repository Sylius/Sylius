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

namespace Sylius\Bundle\ApiBundle\Doctrine;

final class ApiShopRequestTypes
{
    const SHOP_GET = 'SHOP_GET';
    const ITEMS_GET_SUBRESOURCE = 'ITEMS_GET_SUBRESOURCE';
    const SHIPMENTS_GET_SUBRESOURCE = 'SHIPMENTS_GET_SUBRESOURCE';
    const PAYMENTS_GET_SUBRESOURCE = 'PAYMENTS_GET_SUBRESOURCE';
    const ADJUSTMENTS_GET_SUBRESOURCE = 'ADJUSTMENTS_GET_SUBRESOURCE';
    const PAYMENTS_METHODS_GET_SUBRESOURCE = 'PAYMENTS_METHODS_GET_SUBRESOURCE';
    const SHIPMENTS_METHODS_GET_SUBRESOURCE = 'SHIPMENTS_METHODS_GET_SUBRESOURCE';
    const ITEMS_ADJUSTMENTS_GET_SUBRESOURCE = 'ITEMS_ADJUSTMENTS_GET_SUBRESOURCE';
}
