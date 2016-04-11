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

use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyHelper extends Helper implements CurrencyHelperInterface
{
    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var CurrencyConverterInterface
     */
    private $converter;

    /**
     * @var MoneyHelperInterface
     */
    private $moneyHelper;

    /**
     * @var CurrencyProviderInterface
     */
    private $currencyProvider;

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param CurrencyConverterInterface $converter
     * @param MoneyHelperInterface $moneyHelper
     * @param CurrencyProviderInterface $currencyProvider
     */
    public function __construct(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyHelperInterface $moneyHelper,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->currencyContext = $currencyContext;
        $this->converter = $converter;
        $this->moneyHelper = $moneyHelper;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAmount($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();

        return $this->converter->convertFromBase($amount, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndFormatAmount($amount, $currency = null, $decimal = false)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();
        $amount = $this->converter->convertFromBase($amount, $currency);

        return $this->moneyHelper->formatAmount($amount, $currency, $decimal);
    }

    /**
     * @return string
     */
    public function getBaseCurrencySymbol()
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($this->currencyProvider->getBaseCurrency());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency';
    }
}
