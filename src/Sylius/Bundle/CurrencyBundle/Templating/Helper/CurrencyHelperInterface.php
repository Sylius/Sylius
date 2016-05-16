<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Templating\Helper;

use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Axel Vankrunkelsven <axel@digilabs.be>
 */
interface CurrencyHelperInterface
{
    /**
     * @param int $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function convertAmount($amount, $currency = null);

    /**
     * @param int $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function convertAndFormatAmount($amount, $currency = null);

    /**
     * @return string
     */
    public function getBaseCurrencySymbol();
}
