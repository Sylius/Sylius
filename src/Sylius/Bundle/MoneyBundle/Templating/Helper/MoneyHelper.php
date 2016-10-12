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
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var string
     */
    private $defaultCurrencyCode;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param MoneyFormatterInterface $moneyFormatter
     * @param string $defaultCurrencyCode
     * @param string $defaultLocaleCode
     */
    public function __construct(MoneyFormatterInterface $moneyFormatter, $defaultCurrencyCode, $defaultLocaleCode)
    {
        $this->moneyFormatter = $moneyFormatter;
        $this->defaultCurrencyCode = $defaultCurrencyCode;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currencyCode = null, $localeCode = null)
    {
        $localeCode = $localeCode ?: $this->defaultLocaleCode;
        $currencyCode = $currencyCode ?: $this->defaultCurrencyCode;

        return $this->moneyFormatter->format($amount, $currencyCode, $localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
