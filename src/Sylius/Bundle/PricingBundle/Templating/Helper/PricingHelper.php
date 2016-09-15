<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\Templating\Helper;

use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PricingHelper extends Helper
{
    /**
     * @var DelegatingCalculatorInterface
     */
    protected $priceCalculator;

    /**
     * @param DelegatingCalculatorInterface $priceCalculator
     */
    public function __construct(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * @param PriceableInterface $priceable
     * @param array $context
     *
     * @return int
     */
    public function calculatePrice(PriceableInterface $priceable, array $context = [])
    {
        return $this->priceCalculator->calculate($priceable, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_pricing';
    }
}
