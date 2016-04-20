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
    private $defaultCurrency;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @param string $defaultCurrency
     * @param string $defaultLocale
     * @param MoneyFormatterInterface $moneyFormatter
     */
    public function __construct($defaultCurrency, $defaultLocale, MoneyFormatterInterface $moneyFormatter)
    {
        $this->defaultCurrency = $defaultCurrency;
        $this->defaultLocale = $defaultLocale;
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currency = null, $locale = null)
    {
        $locale = $locale ?: $this->defaultLocale;
        $currency = $currency ?: $this->defaultCurrency;

        return $this->moneyFormatter->format($amount, $currency, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
