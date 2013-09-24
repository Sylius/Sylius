<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Twig;

use Sylius\Bundle\MoneyBundle\Converter\CurrencyConverterInterface;
use Sylius\Bundle\MoneyBundle\Context\CurrencyContextInterface;
use Twig_Extension;
use Twig_Filter_Method;
use Locale;
use NumberFormatter;

/**
 * Sylius money twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusMoneyExtension extends Twig_Extension
{
    protected $currencyContext;
    protected $formatter;
    protected $converter;

    public function __construct(CurrencyContextInterface $currencyContext, CurrencyConverterInterface $converter, $locale = null)
    {
        $this->currencyContext = $currencyContext;
        $this->converter = $converter;

        $locale = $locale ?: Locale::getDefault();
        $this->formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'sylius_money' => new Twig_Filter_Method($this, 'formatMoney', array('is_safe' => array('html'))),
            'sylius_price' => new Twig_Filter_Method($this, 'formatPrice', array('is_safe' => array('html'))),
        );
    }

    /**
     * Format the money amount to nice display form.
     *
     * @param integer $amount
     * @param string  $currency
     *
     * @return string
     */
    public function formatMoney($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getDefaultCurrency();
        $result = $this->formatter->formatCurrency($amount / 100, $currency);

        if (false === $result) {
            throw new \InvalidArgumentException(sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency));
        }

        return str_replace(' ', ' ', $result);
    }

    /**
     * Convert price between currencies and format the amount to nice display form.
     *
     * @param integer $amount
     * @param string  $currency
     *
     * @return string
     */
    public function formatPrice($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();
        $amount = $this->converter->convert($amount, $currency);

        return $this->formatMoney($amount, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
