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

use Sylius\Bundle\MoneyBundle\Twig\MoneyExtension as BaseMoneyExtension;
use Sylius\Component\Core\Model\PriceableInterface;

/**
 * Sylius money Twig helper.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class MoneyExtension extends BaseMoneyExtension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_calculate_price', array($this, 'calculatePrice')),
        );
    }

    public function calculatePrice(PriceableInterface $priceable)
    {
        return $this->helper->calculatePrice($priceable);
    }
}
