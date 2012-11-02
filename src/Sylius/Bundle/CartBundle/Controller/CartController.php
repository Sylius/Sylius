<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cart frontend controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartController extends ResourceController
{
    /**
     * Displays cart.
     *
     * @param Request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $cart = $this->getCurrentCart();
        $form = $this->createForm($this->getResourceFormType(), $cart);

        return $this->renderResponse('show.html', array(
            'cart' => $cart,
            'form' => $form->createView()
        ));
    }

    /**
     * Adds item to cart.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addItemAction(Request $request)
    {
        $cart = $this->getCurrentCart();
        $emptyItem = $this->getCartItemManager()->create();

        $item = $this->getResolver()->resolve($emptyItem, $request);

        if (!$item) {
            $this->setFlash('error', 'sylius_cart.flashes.add.error');

            return $this->redirectToCart();
        }

        $cartOperator = $this->getOperator();

        $cartOperator
            ->addItem($cart, $item)
            ->refresh($cart)
        ;

        $errors = $this->get('validator')->validate($cart);

        if (0 === count($errors)) {
            $this->setFlash('success', 'sylius_cart.flashes.add.success');
            $cartOperator->save($cart);
        }

        return $this->redirectToCart();
    }

    /**
     * Removes item from cart.
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function removeItemAction(Request $request, $id)
    {
        $cart = $this->getCurrentCart();
        $item = $this->getCartItemManager()->find($id);

        if (!$item || false === $cart->hasItem($item)) {
            $this->setFlash('error', 'sylius_cart.flashes.remove.error');

            return $this->redirectToCart();
        }

        $this
            ->getOperator()
            ->removeItem($cart, $item)
            ->refresh($cart)
            ->save($cart)
        ;

        $this->setFlash('success', 'sylius_cart.flashes.remove.success');

        return $this->redirectToCart();
    }

    /**
     * Saves cart.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request)
    {
        $cart = $this->getCurrentCart();

        $form = $this
            ->createForm($this->getResourceFormType(), $cart)
            ->bind($request)
        ;

        if ($form->isValid()) {
            $this
                ->getOperator()
                ->refresh($cart)
                ->save($cart)
            ;

            $this->setFlash('success', 'sylius_cart.flashes.saved');
        }

        return $this->renderResponse('show.html', array(
            'cart' => $cart,
            'form' => $form->createView()
        ));
    }

    /**
     * Clears cart.
     *
     * @return Response
     */
    public function clearAction()
    {
        $this
            ->getOperator()
            ->clear($this->getCurrentCart())
        ;

        $this->setFlash('success', 'sylius_cart.flashes.cleared');

        return $this->redirectToCart();
    }

    /**
     * Redirect to show cart action.
     *
     * @return RedirectResponse
     */
    protected function redirectToCart()
    {
        return $this->redirect($this->generateUrl($this->getCartRoute()));
    }

    /**
     * Cart show action route.
     *
     * @return string
     */
    protected function getCartRoute()
    {
        return 'sylius_cart_show';
    }

    /**
     * Get current cart.
     *
     * @return CartInterface
     */
    protected function getCurrentCart()
    {
        return $this
            ->getProvider()
            ->getCart()
        ;
    }

    /**
     * Get cart item manager.
     *
     * @return ResourceManagerInterface
     */
    protected function getCartItemManager()
    {
        return $this->get('sylius_cart.manager.item');
    }

    /**
     * Get cart provider.
     *
     * @return CartProviderInterface
     */
    protected function getProvider()
    {
        return $this->get('sylius_cart.provider');
    }

    /**
     * Get cart operator.
     *
     * @return CartOperatorInterface
     */
    protected function getOperator()
    {
        return $this->get('sylius_cart.operator');
    }

    /**
     * Get cart item resolver.
     *
     * @return CartResolverInterface
     */
    protected function getResolver()
    {
        return $this->get('sylius_cart.resolver');
    }

    /**
     * {@inheritdoc}
     */
    protected function getResourceFormType()
    {
        return 'sylius_cart';
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'sylius_cart';
    }

    /**
     * {@inheritdoc}
     */
    protected function getResourceName()
    {
        return 'cart';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateNamespace()
    {
        return 'SyliusCartBundle:Cart';
    }
}
