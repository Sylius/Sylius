<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Templating\Helper;

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Templating\Helper\Helper;

class CartHelper extends Helper
{
    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * Cart item manager.
     *
     * @var FactoryInterface
     */
    protected $cartItemFactory;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor.
     *
     * @param CartProviderInterface $cartProvider
     * @param FactoryInterface   $cartItemFactory
     * @param FormFactoryInterface  $formFactory
     */
    public function __construct(CartProviderInterface $cartProvider, FactoryInterface $cartItemFactory, FormFactoryInterface $formFactory)
    {
        $this->cartProvider = $cartProvider;
        $this->cartItemFactory = $cartItemFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns current cart.
     *
     * @return null|CartInterface
     */
    public function getCurrentCart()
    {
        return $this->cartProvider->getCart();
    }

    /**
     * Check if a cart exists.
     *
     * @return Boolean
     */
    public function hasCart()
    {
        return $this->cartProvider->hasCart();
    }

    /**
     * Returns cart item form view.
     *
     * @param array $options
     *
     * @return FormView
     */
    public function getItemFormView(array $options = array())
    {
        $form = $this->formFactory->create('sylius_cart_item', $this->cartItemFactory->createNew(), $options);

        return $form->createView();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_cart';
    }
}
