<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartsBundle\Twig;

use Sylius\Bundle\CartsBundle\Provider\CartProviderInterface;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Sylius cart engine twig extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartsExtension extends Twig_Extension
{
    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    private $cartProvider;

    /**
     * Constructor.
     *
     * @param CartProviderInterface $cartProvider
     */
    public function __construct(CartProviderInterface $cartProvider)
    {
        $this->cartProvider = $cartProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_carts_get'         => new Twig_Function_Method($this, 'getCurrentCart'),
        );
    }

    /**
     * Returns current cart.
     *
     * @return CartInterface
     */
    public function getCurrentCart()
    {
        return $this->cartProvider->getCart();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_carts';
    }
}
