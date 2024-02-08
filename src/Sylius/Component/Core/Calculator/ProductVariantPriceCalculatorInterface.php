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

namespace Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @deprecated since Sylius 1.8, use ProductVariantPricesCalculatorInterface instead
 */
interface ProductVariantPriceCalculatorInterface
{
    /**
     * @throws MissingChannelConfigurationException when price for given channel does not exist
     */
    public function calculate(ProductVariantInterface $productVariant, array $context): int;
}
