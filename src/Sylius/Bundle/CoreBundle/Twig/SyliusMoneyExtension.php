<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension as BaseSyliusMoneyExtension;
use Sylius\Bundle\MoneyBundle\Context\CurrencyContextInterface;
use Sylius\Bundle\MoneyBundle\Converter\CurrencyConverterInterface;
use Sylius\Bundle\CoreBundle\Calculator\PriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\Model\VariantInterface;

/**
 * Sylius money Twig helper.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusMoneyExtension extends BaseSyliusMoneyExtension
{
    protected $priceCalculator;

    public function __construct(
        PriceCalculatorInterface $priceCalculator,
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        $locale = null
    )
    {
        $this->priceCalculator = $priceCalculator;

        parent::__construct($currencyContext, $converter, $locale);
    }

    public function getFilters()
    {
        return array_merge(parent::getFilters(), array(
            new \Twig_SimpleFilter('sylius_variant_price', array($this, 'formatVariantPrice'), array('is_safe' => array('html'))),
        ));
    }

    public function formatVariantPrice(VariantInterface $variant, $currency = null)
    {
        return $this->formatPrice(
            $this->priceCalculator->calculate($variant),
            $currency
        );
    }
}
