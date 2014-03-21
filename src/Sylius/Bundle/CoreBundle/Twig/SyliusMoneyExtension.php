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
use Sylius\Component\Money\Context\CurrencyContextInterface;
use Sylius\Component\Money\Converter\CurrencyConverterInterface;
use Sylius\Component\Core\Calculator\PriceCalculatorInterface;
use Sylius\Component\Core\Model\PriceableInterface;

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

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_calculate_price', array($this, 'calculatePrice'), array('is_safe' => array('html'))),
        );
    }

    public function calculatePrice(PriceableInterface $priceable)
    {
        return $this->priceCalculator->calculate($priceable);
    }
}
