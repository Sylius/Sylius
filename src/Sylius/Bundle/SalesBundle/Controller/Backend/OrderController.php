<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sylius\Bundle\SalesBundle\EventDispatcher\SyliusSalesEvents;
use Sylius\Bundle\SalesBundle\EventDispatcher\Event\FilterOrderEvent;

/**
 * Backend orders controller.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderController extends ContainerAware
{
	/**
     * Displays all orders.
     */
    public function listAction()
    {       
    	$orderManager = $this->container->get('sylius_sales.manager.order');
    	$paginator = $orderManager->createPaginator();
    	
    	$paginator->setCurrentPage($this->container->get('request')->query->get('page', 1));
    	
    	$orders = $paginator->getCurrentPageResults();
    	
        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:list.html.' . $this->getEngine(), array(
        	'orders' => $orders,
         	'paginator' => $paginator
        ));
    }
    
    /**
     * Shows an order.
     */
    public function showAction($id)
    {
        $order = $this->container->get('sylius_sales.manager.order')->findOrder($id);
        
        if (!$order) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }
        
        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:show.html.' . $this->getEngine(), array(
        	'order' => $order
        ));
    }
    
	/**
     * Order status management.
     */
    public function statusAction($id)
    {
        $order = $this->container->get('sylius_sales.manager.order')->findOrder($id);
        
        if (!$order) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }
        
        $request = $this->container->get('request');
        
        $form = $this->container->get('form.factory')->create($this->container->get('sylius_sales.form.type.status'));
        $form->setData($order);
        
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_STATUS, new FilterOrderEvent($order));
                $this->container->get('sylius_sales.manipulator.order')->status($order);
               
                return new RedirectResponse($request->headers->get('referer'));
            }
        }
        
        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:status.html.' . $this->getEngine(), array(
        	'form' => $form->createView(),
        	'order' => $order
        ));
    }
    
	/**
     * Confirms order.
     */
    public function confirmAction($id)
    {
        $order = $this->container->get('sylius_sales.manager.order')->findOrder($id);
        
        if (!$order) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }
        
        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_CONFIRM, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->confirm($order);
        
        return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_order_list'));
    }
    
	/**
     * Closes order.
     */
    public function closeAction($id)
    {
        $order = $this->container->get('sylius_sales.manager.order')->findOrder($id);
        
        if (!$order) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }
        
        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_CLOSE, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->close($order);
        
        return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_order_list'));
    }
    
	/**
     * Opens order.
     */
    public function openAction($id)
    {
        $order = $this->container->get('sylius_sales.manager.order')->findOrder($id);
        
        if (!$order) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }
        
        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_OPEN, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->open($order);
        
        return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_order_list'));
    }    
    
	/**
     * Deletes order.
     */
    public function deleteAction($id)
    {
        $order = $this->container->get('sylius_sales.manager.order')->findOrder($id);
        
        if (!$order) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }
        
        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_DELETE, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->delete($order);
        
        return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_order_list'));
    }
    
    /**
     * Returns templating engine name.
     * 
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('sylius_sales.engine');
    }
}
