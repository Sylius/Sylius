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
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Jonathan Douet <jonathan.douet@smile.eu>
 */
class CurrencyHelper extends Helper implements CurrencyHelperInterface
{
    /**
     * @var LocaleContextInterface
     */
    protected $localeContext;

    /**
     * @var CurrencyHelperInterface
     */
    private $decoratedCurrencyHelper;


    public function __construct(
        CurrencyHelperInterface $decoratedCurrencyHelper,
        LocaleContextInterface $localeContext
    ) {
        $this->decoratedCurrencyHelper = $decoratedCurrencyHelper;
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndFormatAmount($amount, $currency = null)
    {
        $currency = $currency ?: $this->decoratedCurrencyHelper->getCurrency();
        $amount = $this->convertAmount($amount, $currency);
        $locale = $this->localeContext->getCurrentLocale();

        return $this->formatAmount($amount, $currency, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function convertAmount($amount, $currency = null)
    {
        return $this->decoratedCurrencyHelper->convertAmount($amount, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currency, $locale = null)
    {
        return $this->decoratedCurrencyHelper->formatAmount($amount, $currency, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCurrencySymbol()
    {
        return $this->decoratedCurrencyHelper->getBaseCurrencySymbol();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency_decorator';
    }
}
