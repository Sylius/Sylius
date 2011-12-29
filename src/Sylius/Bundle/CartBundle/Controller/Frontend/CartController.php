<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Controller\Frontend;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sylius\Bundle\CartBundle\EventDispatcher\Event\CartOperationEvent;
use Sylius\Bundle\CartBundle\EventDispatcher\Event\FilterCartEvent;
use Sylius\Bundle\CartBundle\EventDispatcher\SyliusCartEvents;

/**
 * Cart frontend controller.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartController extends ContainerAware
{
    /**
     * Displays cart.
     */
    public function showAction()
    {
    	$cart = $this->container->get('sylius_cart.provider')->getCart();
    	
    	$form = $this->container->get('form.factory')->create($this->container->get('sylius_cart.form.type.cart'));
    	$form->setData($cart);
    	
        return $this->container->get('templating')->renderResponse('SyliusCartBundle:Frontend/Cart:show.html.' . $this->getEngine(), array(
        	'cart' => $cart,
        	'form' => $form->createView()
        ));
    }
    
	/**
     * Adds item to cart.
     */
    public function addItemAction()
    {
        $itemManager = $this->container->get('sylius_cart.manager.item');
        $item = $itemManager->createItem();
        
        $form = $this->container->get('form.factory')->create($this->container->get('sylius_cart.form.type.item'));
        $form->setData($item);
        $form->bindRequest($this->container->get('request'));
        
        if ($form->isValid()) {
            $cart = $this->container->get('sylius_cart.provider')->getCart();

            $this->container->get('event_dispatcher')->dispatch(SyliusCartEvents::ITEM_ADD, new CartOperationEvent($item, $cart));
            
            $cartOperator = $this->container->get('sylius_cart.operator');
            $cartOperator->addItem($cart, $item);        
            $cartOperator->refreshCart($cart);
            
            $cartManager = $this->container->get('sylius_cart.manager.cart');
            
            $cartManager->persistCart($cart);
            $cartManager->flushCarts();
        }
        
        return new RedirectResponse($this->container->get('router')->generate('sylius_cart_show'));
    }
    
    /**
     * Removes item from cart.
     */
    public function removeItemAction($id)
    {
        $itemManager = $this->container->get('sylius_cart.manager.item');
        $item = $itemManager->findItem($id);
        
        if (!$item) {
            throw new NotFoundHttpException('This cart item does not exist.');
        }
        
        $cart = $this->container->get('sylius_cart.provider')->getCart();

        if ($item->getCart() !== $cart) {
            throw new NotFoundHttpException('This cart item is not accessible.');
        }
        
        $cartOperator = $this->container->get('sylius_cart.operator');
        
        $this->container->get('event_dispatcher')->dispatch(SyliusCartEvents::ITEM_REMOVE, new CartOperationEvent($item, $cart));
        $cartOperator->removeItem($cart, $item);
        $cartOperator->refreshCart($cart);
        
        $itemManager->removeItem($item);
        
        $this->container->get('sylius_cart.manager.cart')->persistCart($cart);
        
        return new RedirectResponse($this->container->get('router')->generate('sylius_cart_show'));
    }
    
    /**
     * Saves cart.
     */
    public function updateAction()
    {  
        $cart = $this->container->get('sylius_cart.provider')->getCart();
        
    	$form = $this->container->get('form.factory')->create($this->container->get('sylius_cart.form.type.cart'));
    	$form->setData($cart);
    	$form->bindRequest($this->container->get('request'));
    	
    	if ($form->isValid()) {
    	    $cartOperator = $this->container->get('sylius_cart.operator');
    	    
        	$existingItems = array();
            
            foreach ($cart->getItems() as $item) {
                $existingItems[] = $item;
            }
    	    
    	    $cart->clearItems();
    	    
    	    foreach($existingItems as $item) {
    	        $cartOperator->addItem($cart, $item);
    	    }
    	    
    	    $this->container->get('event_dispatcher')->dispatch(SyliusCartEvents::CART_UPDATE, new FilterCartEvent($cart));
    	    $cartOperator->refreshCart($cart);
    	    
    	    $this->container->get('sylius_cart.manager.cart')->persistCart($cart);
    	}
    	
        return new RedirectResponse($this->container->get('router')->generate('sylius_cart_show'));
    }
    
    /**
     * Removes cart.
     */
    public function clearAction()
    {
        $this->container->get('sylius_cart.manager.cart')->removeCart($this->container->get('sylius_cart.provider')->getCart());
        
        return new RedirectResponse($this->container->get('router')->generate('sylius_cart_show'));
    }
    
    /**
     * Returns templating engine name.
     * 
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('sylius_cart.engine');
    }
}
