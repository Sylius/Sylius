<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ProductVariantPriceCalculatorInterface
{
    /**
     * @param ProductVariantInterface $productVariant
     * @param ChannelInterface $channel
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public function calculate(ProductVariantInterface $productVariant, ChannelInterface $channel);
}
