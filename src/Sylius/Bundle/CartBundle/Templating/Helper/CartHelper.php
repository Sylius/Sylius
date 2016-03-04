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

use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Templating\Helper\Helper;

class CartHelper extends Helper
{
    /**
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * @var FactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    protected $orderItemQuantityModifier;

    /**
     * @param CartProviderInterface $cartProvider
     * @param FactoryInterface $cartItemFactory
     * @param FormFactoryInterface $formFactory
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     */
    public function __construct(CartProviderInterface $cartProvider, FactoryInterface $cartItemFactory, FormFactoryInterface $formFactory, OrderItemQuantityModifierInterface $orderItemQuantityModifier)
    {
        $this->cartProvider = $cartProvider;
        $this->cartItemFactory = $cartItemFactory;
        $this->formFactory = $formFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    /**
     * @return CartInterface|null
     */
    public function getCurrentCart()
    {
        return $this->cartProvider->getCart();
    }

    /**
     * @return bool
     */
    public function hasCart()
    {
        return $this->cartProvider->hasCart();
    }

    /**
     * @param array $options
     *
     * @return FormView
     */
    public function getItemFormView(array $options = [])
    {
        $cartItem = $this->cartItemFactory->createNew();
        $this->orderItemQuantityModifier->modify($cartItem, 1);

        $form = $this->formFactory->create('sylius_cart_item', $cartItem, $options);

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
