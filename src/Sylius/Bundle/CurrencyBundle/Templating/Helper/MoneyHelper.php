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
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MoneyHelper implements MoneyHelperInterface
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @param string $defaultLocale
     * @param CurrencyContextInterface $currencyContext
     * @param MoneyFormatterInterface $moneyFormatter
     */
    public function __construct(
        $defaultLocale,
        CurrencyContextInterface $currencyContext,
        MoneyFormatterInterface $moneyFormatter
    ) {
        $this->defaultLocale = $defaultLocale;
        $this->currencyContext = $currencyContext;
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currency = null, $locale = null)
    {
        $locale = $locale ?: $this->defaultLocale;
        $currency = $currency ?: $this->currencyContext->getCurrency();

        return $this->moneyFormatter->format($amount, $currency, $locale);
    }
}
