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

use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelper as BaseMoneyHelper;
use Sylius\Component\Core\Calculator\PriceCalculatorInterface;
use Sylius\Component\Core\Model\PriceableInterface;
use Sylius\Component\Money\Context\CurrencyContextInterface;
use Sylius\Component\Money\Converter\CurrencyConverterInterface;

/**
 * Sylius core money templating helper.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class MoneyHelper extends BaseMoneyHelper
{
    private $priceCalculator;

    public function __construct(
        PriceCalculatorInterface $priceCalculator,
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        $locale = null
    )
    {
        $this->priceCalculator = $priceCalculator;

        parent::__construct($currencyContext, $converter, $locale);
    }

    /**
     * @param PriceableInterface $priceable
     *
     * @return integer
     */
    public function calculatePrice(PriceableInterface $priceable)
    {
        return $this->priceCalculator->calculate($priceable);
    }
}
