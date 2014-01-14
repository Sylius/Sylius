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

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;

/**
 * Sylius cart engine twig extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartExtension extends \Twig_Extension
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
     * @var RepositoryInterface
     */
    protected $cartItemRepository;

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
     * @param RepositoryInterface   $cartItemRepository
     * @param FormFactoryInterface  $formFactory
     */
    public function __construct(CartProviderInterface $cartProvider, RepositoryInterface $cartItemRepository, FormFactoryInterface $formFactory)
    {
        $this->cartProvider = $cartProvider;
        $this->cartItemRepository = $cartItemRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
             new \Twig_SimpleFunction('sylius_cart_exists', array($this, 'hasCart')),
             new \Twig_SimpleFunction('sylius_cart_get', array($this, 'getCurrentCart')),
             new \Twig_SimpleFunction('sylius_cart_form', array($this, 'getItemFormView')),
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
     * Check if a cart exists.
     *
     * @return boolean
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
        $item = $this->cartItemRepository->createNew();
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
