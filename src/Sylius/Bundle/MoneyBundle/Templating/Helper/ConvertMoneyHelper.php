<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

class ConvertMoneyHelper extends Helper implements ConvertMoneyHelperInterface
{
    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAmount($amount, $sourceCurrencyCode, $targetCurrencyCode)
    {
        return $this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money_converter';
    }
}
