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
     * The locale used to format money.
     *
     * @var string
     */
    private $locale;

    /**
     * The default currency.
     *
     * @var string
     */
    private $currency;

    /**
     * @var \NumberFormatter
     */
    private $formatter;

    public function __construct($locale, $currency)
    {
        $this->locale = $locale;
        $this->currency = $currency;
        $this->formatter = new \NumberFormatter($locale ?: \Locale::getDefault(), \NumberFormatter::CURRENCY);
    }

    /**
     * Format the money amount to nice display form.
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function formatMoney($amount, $currency = null)
    {
        $currency = $currency ?: $this->getDefaultCurrency();
        $result = $this->formatter->formatCurrency($amount / 100, $currency);

        if (false === $result) {
            throw new \InvalidArgumentException(sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency));
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }

    /**
     * Get the default currency if none is provided as argument.
     *
     * @return string The currency code
     */
    protected function getDefaultCurrency()
    {
        return $this->currency;
    }
}
