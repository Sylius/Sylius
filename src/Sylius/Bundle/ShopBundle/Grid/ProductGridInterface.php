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

namespace Sylius\Bundle\ShopBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;

/** @experimental */
interface ProductGridInterface
{
    public const NAME = 'sylius_shop_product';

    public function __invoke(GridBuilderInterface $gridBuilder): void;
}
