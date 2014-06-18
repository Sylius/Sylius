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

use Sylius\Component\Money\Context\CurrencyContextInterface;
use Sylius\Component\Money\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

class MoneyHelper extends Helper
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
     * @var \NumberFormatter
     */
    private $formatter;

    /**
     * @var string
     */
    private $pattern = '¤#,##0.00;-¤#,##0.00';

    public function __construct(CurrencyContextInterface $currencyContext, CurrencyConverterInterface $converter, $locale = null)
    {
        $this->currencyContext = $currencyContext;
        $this->converter       = $converter;
        $this->formatter       = new \NumberFormatter($locale ?: \Locale::getDefault(), \NumberFormatter::CURRENCY);
        $this->formatter->setPattern($this->pattern);
    }

    /**
     * Format the money amount to nice display form.
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function formatMoney($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getDefaultCurrency();
        $result   = $this->formatter->formatCurrency($amount / 100, $currency);

        if (false === $result) {
            throw new \InvalidArgumentException(sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency));
        }

        return $result;
    }

    /**
     * Convert price between currencies and format the amount to nice display form.
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function formatPrice($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();

        return $this->formatMoney($this->converter->convert($amount, $currency), $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
