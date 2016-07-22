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

use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

class PriceHelper extends Helper implements PriceHelperInterface
{
    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @var MoneyHelperInterface
     */
    private $moneyHelper;

    /**
     * @param CurrencyConverterInterface $currencyConverter
     * @param MoneyHelperInterface $moneyHelper
     */
    public function __construct(CurrencyConverterInterface $currencyConverter, MoneyHelperInterface $moneyHelper)
    {
        $this->currencyConverter = $currencyConverter;
        $this->moneyHelper = $moneyHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndFormatAmount($amount, $currencyCode = null, $locale = null)
    {
        if (null !== $currencyCode) {
            $amount = $this->currencyConverter->convertFromBase($amount, $currencyCode);
        }

        return $this->moneyHelper->formatAmount($amount, $currencyCode, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price';
    }
}
