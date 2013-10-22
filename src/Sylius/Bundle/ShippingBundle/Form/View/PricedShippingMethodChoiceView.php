<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\View;

use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Represents a Shipping Method choice in templates.
 * Makes the calculated price for the choice available to the template.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class PricedShippingMethodChoiceView extends ChoiceView
{
    /**
     * The price of this shipping method choice
     *
     * @var float
     */
    public $price;

    public function __construct($data, $value, $label, $price)
    {
        $this->price = $price;

        parent::__construct($data, $value, $label);
    }
}