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
use Sylius\Component\Resource\Repository\RepositoryInterface;
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
    private $cartProvider;

    /**
     * Cart item manager.
     *
     * @var RepositoryInterface
     */
    private $cartItemRepository;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $formFactory;

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
        $form = $this->formFactory->create('sylius_cart_item', $this->cartItemRepository->createNew(), $options);

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
