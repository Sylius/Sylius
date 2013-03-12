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
    public function getFilters()
    {
        return array(
            'sylius_money' => new Twig_Filter_Method($this, 'formatMoney', array('is_safe' => array('html'))),
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
        $currency = $currency ?: $this->defaultCurrency;
        $result = $this->formatter->formatCurrency($amount / 100, $currency);

        if (false === $result) {
            throw new \InvalidArgumentException(sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency));
        }

        return str_replace(' ', ' ', $result);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
