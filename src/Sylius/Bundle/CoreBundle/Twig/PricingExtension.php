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

use Sylius\Bundle\PricingBundle\Twig\PricingExtension as BasePricingExtension;
use Sylius\Component\Pricing\Model\PriceableInterface;

class PricingExtension extends BasePricingExtension
{
    /**
     * Returns calculated price for given priceable and currency.
     *
     * @param PriceableInterface $priceable
     * @param array              $context
     * @param string|null        $currency
     *
     * @return integer
     */
    public function calculatePrice(PriceableInterface $priceable, array $context = array(), $currency = null)
    {
        return $this->helper->calculatePrice($priceable, $context, $currency);
    }
}
