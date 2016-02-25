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

class MoneyHelper extends Helper implements MoneyHelperInterface
{
    /**
     * The default currency.
     *
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale   The locale used to format money.
     * @param string $currency The default currency.
     */
    public function __construct($locale, $currency = null)
    {
        $this->locale = $locale ?: \Locale::getDefault();
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currency = null, $decimal = false, $locale = null)
    {
        $locale = $locale   ?: $this->getDefaultLocale();
        $currency = $currency ?: $this->getDefaultCurrency();

        if ($decimal) {
            $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        } else {
            $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        }

        $result = $formatter->formatCurrency($amount / 100, $currency);
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

    /**
     * Get the default locale if none is provided as argument.
     *
     * @return string The locale code
     */
    protected function getDefaultLocale()
    {
        return $this->locale;
    }
}
