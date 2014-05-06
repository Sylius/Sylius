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

use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyHelper extends Helper
{
    /**
     * @var CurrencyConverterInterface
     */
    private $converter;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * Money helper.
     *
     * @var MoneyHelper
     */
    private $moneyHelper;

    public function __construct(CurrencyContextInterface $currencyContext, CurrencyConverterInterface $converter, MoneyHelper $moneyHelper)
    {
        $this->currencyContext = $currencyContext;
        $this->converter       = $converter;
        $this->moneyHelper     = $moneyHelper;
    }

    /**
     * Convert amount to target or currently used currency.
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function convertAmount($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();

        return $this->converter->convert($amount, $currency);
    }

    /**
     * Convert amount and format it!
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function convertAndFormatAmount($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();
        $amount = $this->converter->convert($amount, $currency);

        return $this->moneyHelper->formatAmount($amount, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency';
    }
}
