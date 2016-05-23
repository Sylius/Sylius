<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\Intl\Intl;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyHelper implements CurrencyHelperInterface
{
    /**
     * @var LocaleContextInterface
     */
    protected $localeContext;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var CurrencyProviderInterface
     */
    private $currencyProvider;


    public function __construct(
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyFormatterInterface $moneyFormatter,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->localeContext = $localeContext;
        $this->currencyContext = $currencyContext;
        $this->currencyConverter = $converter;
        $this->moneyFormatter = $moneyFormatter;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndFormatAmount($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();
        $amount = $this->currencyConverter->convertFromBase($amount, $currency);
        $locale = $this->localeContext->getCurrentLocale();
        return $this->moneyFormatter->format($amount, $currency, $locale);
    }

    /**
     * @param int $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function convertAmount($amount, $currency = null)
    {
        $currency = $currency ?: $this->currencyContext->getCurrency();

        return $this->currencyConverter->convertFromBase($amount, $currency);
    }

    /**
     * @return string
     */
    public function getBaseCurrencySymbol()
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($this->currencyProvider->getBaseCurrency()->getCode());
    }
}
