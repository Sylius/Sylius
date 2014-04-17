<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\Twig;

use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * Sylius pricing Twig helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusPricingExtension extends \Twig_Extension
{
    /**
     * Price calculator.
     *
     * @var DelegatingCalculatorInterface
     */
    protected $priceCalculator;

    /**
     * Constructor.
     *
     * @param DelegatingCalculatorInterface $priceCalculator
     */
    public function __construct(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_calculate_price', array($this, 'calculatePrice'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns calculated price for given priceable.
     *
     * @param PriceableInterface $priceable
     * @param array              $context
     *
     * @return integer
     */
    public function calculatePrice(PriceableInterface $priceable, array $context = array())
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
