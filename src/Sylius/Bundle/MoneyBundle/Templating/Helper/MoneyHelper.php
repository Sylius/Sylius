<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Symfony\Component\Templating\Helper\Helper;

class MoneyHelper extends Helper implements MoneyHelperInterface
{
    /**
     * @var string
     */
    private $defaultCurrencyCode;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @param string $defaultCurrencyCode
     * @param string $defaultLocale
     * @param MoneyFormatterInterface $moneyFormatter
     */
    public function __construct($defaultCurrencyCode, $defaultLocale, MoneyFormatterInterface $moneyFormatter)
    {
        $this->defaultCurrencyCode = $defaultCurrencyCode;
        $this->defaultLocale = $defaultLocale;
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currencyCode = null, $locale = null)
    {
        $locale = $locale ?: $this->defaultLocale;
        $currencyCode = $currencyCode ?: $this->defaultCurrencyCode;

        return $this->moneyFormatter->format($amount, $currencyCode, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
