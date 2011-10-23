<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Twig;

use Sylius\Bundle\CartBundle\Provider\Provider;

use Twig_Function_Method;
use Twig_Extension;

/**
 * Sylius cart engine twig extension.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartExtension extends Twig_Extension
{
    private $cartProvider;
    
    public function __construct(Provider $cartProvider)
    {
        $this->cartProvider = $cartProvider;
    }
    
    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            'sylius_cart_get'         => new Twig_Function_Method($this, 'getCart', array('is_safe' => array('html'))),
        );
    }
    
    public function getCart()
    {
        return $this->cartProvider->getCart();
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sylius_cart';
    }
}
