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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MoneyHelper implements MoneyHelperInterface
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
     * @param LocaleContextInterface $localeContext
     * @param CurrencyContextInterface $currencyContext
     * @param MoneyFormatterInterface $moneyFormatter
     */
    public function __construct(
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        MoneyFormatterInterface $moneyFormatter
    ) {
        $this->localeContext = $localeContext;
        $this->currencyContext = $currencyContext;
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currency = null, $locale = null)
    {
        $locale = $locale ?: $this->localeContext->getDefaultLocale();
        $currency = $currency ?: $this->currencyContext->getCurrency();

        return $this->moneyFormatter->format($amount, $currency, $locale);
    }
}
