<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ProductVariantsPricesProviderInterface
{
    /**
     * @param ProductInterface $product
     *
     * @return array
     */
    public function provideVariantsPrices(ProductInterface $product);
}
