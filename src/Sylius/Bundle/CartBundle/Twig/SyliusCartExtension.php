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

use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface;
use Symfony\Component\Form\FormFactory;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Sylius cart engine twig extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartExtension extends Twig_Extension
{
    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    private $cartProvider;

    /**
     * Cart item manager.
     *
     * @var ResourceManagerInterface
     */
    private $cartItemManager;

    /**
     * Form factory.
     *
     * @var FormFactory
     */
    private $formFactory;

    /**
     * Constructor.
     *
     * @param CartProviderInterface    $cartProvider
     * @param ResourceManagerInterface $cartItemManager
     * @param FormFactory              $formFactory
     */
    public function __construct(CartProviderInterface $cartProvider, ResourceManagerInterface $cartItemManager, FormFactory $formFactory)
    {
        $this->cartProvider = $cartProvider;
        $this->cartItemManager = $cartItemManager;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_cart_get' => new Twig_Function_Method($this, 'getCurrentCart'),
            'sylius_cart_form' => new Twig_Function_Method($this, 'getItemFormView'),
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
     * Returns cart item form view.
     *
     * @param array $options
     *
     * @return FormView
     */
    public function getItemFormView(array $options = array())
    {
        $item = $this->cartItemManager->create();
        $form = $this->formFactory->create('sylius_cart_item', $item, $options);

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
