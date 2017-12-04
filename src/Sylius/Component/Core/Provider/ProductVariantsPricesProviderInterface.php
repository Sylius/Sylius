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

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface ProductVariantsPricesProviderInterface
{
    /**
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function provideVariantsPrices(ProductInterface $product, ChannelInterface $channel): array;
}
