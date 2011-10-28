<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sylius\Bundle\SalesBundle\EventDispatcher\SyliusSalesEvents;
use Sylius\Bundle\SalesBundle\EventDispatcher\Event\FilterOrderEvent;

/**
 * Frontend order controller.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderController extends ContainerAware
{
	/**
     * Confirms order.
     */
    public function confirmAction($token)
    {
        $order = $this->container->get('sylius_sales.manager.order')->findOrderBy(array('confirmationToken' => $token));
        
        if (!$order) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }
        
        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_CONFIRM, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->confirm($order);
        
        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Frontend/Order:confirmed.html.' . $this->getEngine(), array(
        	'order' => $order
        ));
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
