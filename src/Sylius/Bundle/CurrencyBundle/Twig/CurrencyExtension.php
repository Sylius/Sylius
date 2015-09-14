<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Twig;

use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

/**
 * Sylius currency Twig helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyExtension extends \Twig_Extension
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
     * @var MoneyExtension
     */
    private $moneyExtension;

    public function __construct(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyExtension $moneyExtension
    ) {
        $this->currencyContext = $currencyContext;
        $this->converter       = $converter;
        $this->moneyExtension  = $moneyExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sylius_currency', array($this, 'convertAmount')),
            new \Twig_SimpleFilter('sylius_price', array($this, 'convertAndFormatAmount')),
        );
    }

    /**
     * Convert amount to target or currently used currency.
     *
     * @param int         $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function convertAmount($amount, $currency = null)
    {
        return $this->converter->convert($amount, $currency ?: $this->currencyContext->getCurrency());
    }

    /**
     * Convert amount and format it!
     *
     * @param int         $amount
     * @param string|null $currency
     * @param bool        $decimal
     *
     * @return string
     */
    public function convertAndFormatAmount($amount, $currency = null, $decimal = false)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();

        return $this->moneyExtension->formatAmount($this->converter->convert($amount, $currency), $currency, $decimal);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency';
    }
}
