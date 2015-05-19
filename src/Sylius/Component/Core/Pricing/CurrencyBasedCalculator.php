<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Pricing;

use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CurrencyBasedCalculator implements CurrencyAwareCalculatorInterface
{
    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    public function __construct(CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = array())
    {
        $currency = $this->currencyContext->getCurrency();

        if (!isset($configuration[$currency])) {
            return $subject->getPrice();
        }

        return (int) $configuration[$currency];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::CURRENCY_BASED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrencySpecific()
    {
        return true;
    }
}
