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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
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
    private $currencyConverter;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var CurrencyProviderInterface
     */
    private $currencyProvider;

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param CurrencyConverterInterface $converter
     * @param MoneyFormatterInterface $moneyFormatter
     * @param CurrencyProviderInterface $currencyProvider
     */
    public function __construct(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyFormatterInterface $moneyFormatter,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->currencyContext = $currencyContext;
        $this->currencyConverter = $converter;
        $this->moneyFormatter = $moneyFormatter;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAmount($amount, $currencyCode = null)
    {
        $currencyCode = $currencyCode ?: $this->currencyContext->getCurrency()->getCode();

        return $this->currencyConverter->convertFromBase($amount, $currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndFormatAmount($amount, $currencyCode = null)
    {
        $currencyCode = $currencyCode ?: $this->currencyContext->getCurrency()->getCode();
        $amount = $this->currencyConverter->convertFromBase($amount, $currencyCode);

        return $this->moneyFormatter->format($amount, $currencyCode);
    }

    /**
     * @return string
     */
    public function getDefaultCurrencySymbol()
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($this->currencyProvider->getDefaultCurrency()->getCode());
    }

    /**
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->currencyProvider->getDefaultCurrency()->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency';
    }
}
