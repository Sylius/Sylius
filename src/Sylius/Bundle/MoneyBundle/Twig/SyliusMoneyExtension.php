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

use Money\Currency;
use Money\Money;
use Twig_Extension;
use Twig_Filter_Method;
use Twig_Function_Method;

/**
 * Sylius money twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusMoneyExtension extends Twig_Extension
{
    protected $defaultCurrency;
    protected $formatter;

    public function __construct($defaultCurrency, $locale = null)
    {
        $this->defaultCurrency = $defaultCurrency;

        $locale = $locale ?: \Locale::getDefault();
        $this->formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_money_convert' => new Twig_Function_Method($this, 'convertToMoney', array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'sylius_money' => new Twig_Filter_Method($this, 'formatMoney', array('is_safe' => array('html'))),
        );
    }

    public function formatMoney(Money $money)
    {
        $amount = $money->getAmount();
        $currency = $money->getCurrency();

        $result = $this->formatter->formatCurrency($amount, $currency);

        if (false === $result) {
            throw new \InvalidArgumentException(sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency));
        }

        return $result;
    }

    public function convertToMoney($amount, $currency = null)
    {
        $amount = is_float($amount) ? $amount * 100 : $amount;
        $amount = (int) $amount;

        $currency = null === $currency ? $this->defaultCurrency : $currency;

        return new Money($amount, new Currency($currency));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
