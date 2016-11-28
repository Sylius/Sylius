<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ChannelBasedPriceHelperInterface
{
    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return int
     */
    public function getPriceForCurrentChannel(ProductVariantInterface $productVariant);
}
