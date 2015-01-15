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

use Symfony\Component\Templating\Helper\Helper;

class MoneyHelper extends Helper
{
    /**
     * The default currency.
     *
     * @var string
     */
    private $currency;

    /**
     * @var \NumberFormatter
     */
    private $formatterCurrency;

    /**
     * @var \NumberFormatter
     */
    private $formatterDecimal;

    /**
     * @param string $locale   The locale used to format money.
     * @param string $currency The default currency.
     */
    public function __construct($locale, $currency)
    {
        $this->currency          = $currency;
        $this->formatterCurrency = new \NumberFormatter($locale ?: \Locale::getDefault(), \NumberFormatter::CURRENCY);
        $this->formatterDecimal  = new \NumberFormatter($locale ?: \Locale::getDefault(), \NumberFormatter::DECIMAL);
    }

    /**
     * Format the money amount to nice display form.
     *
     * @param int         $amount
     * @param string|null $currency
     * @param bool        $decimal
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function formatAmount($amount, $currency = null, $decimal = false)
    {
        if ($decimal) {
            $formatter = $this->formatterDecimal;
        } else {
            $formatter = $this->formatterCurrency;
        }

        $currency = $currency ?: $this->getDefaultCurrency();
        $result   = $formatter->formatCurrency($amount / 100, $currency);
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
