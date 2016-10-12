<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

/**
 * @author Axel Vankrunkelsven <axel@digilabs.be>
 */
interface PriceHelperInterface
{
    /**
     * @param int $amount
     * @param string|null $currencyCode
     * @param float|null $exchangeRate
     * @param string|null $localeCode
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertAndFormatAmount($amount, $currencyCode = null, $exchangeRate = null, $localeCode = null);
}
