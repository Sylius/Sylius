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
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculator as BaseDelegatingCalculator;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

class DelegatingCalculator extends BaseDelegatingCalculator
{
    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    /**
     * @var CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @param ServiceRegistryInterface   $registry
     * @param CurrencyContextInterface   $currencyContext
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(ServiceRegistryInterface $registry, CurrencyContextInterface $currencyContext, CurrencyConverterInterface $currencyConverter)
    {
        parent::__construct($registry);

        $this->currencyContext   = $currencyContext;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $context = array(), $currency = null)
    {
        $price = parent::calculate($subject, $context);

        if (!$this->isConversionNeeded($subject)) {
            return $price;
        }

        return $this->currencyConverter->convert($price, $currency ?: $this->currencyContext->getCurrency());
    }

    /**
     * Returns true if calculator's output needs currency conversion
     *
     * @param PriceableInterface $subject
     *
     * @return bool
     */
    protected function isConversionNeeded(PriceableInterface $subject)
    {
        $calculator = $this->registry->get($subject->getPricingCalculator());

        return ! (in_array('Sylius\Component\Core\Pricing\CurrencyAwareCalculatorInterface', class_implements($calculator)) && $calculator->isCurrencySpecific());
    }
}
