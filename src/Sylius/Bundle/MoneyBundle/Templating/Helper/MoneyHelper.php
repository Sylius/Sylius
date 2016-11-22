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
    private $defaultLocaleCode;

    /**
     * @param MoneyFormatterInterface $moneyFormatter
     * @param string $defaultLocaleCode
     */
    public function __construct(MoneyFormatterInterface $moneyFormatter, $defaultLocaleCode)
    {
        $this->moneyFormatter = $moneyFormatter;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currencyCode, $localeCode = null)
    {
        $localeCode = $localeCode ?: $this->defaultLocaleCode;

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
