<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends FOSRestController
{
    public function proceedAction(Request $request, $orderId)
    {
        $order = $this->findOrderOr404($orderId);
        $state = $order->getCheckoutState();

        if (OrderInterface::CHECKOUT_STATE_COMPLETED === $state) {
            throw new \Exception('Order is already completed.');
        }

        $stateMachine = $this->get('sm.factory')->get($order, OrderCheckoutTransitions::GRAPH);

        if (null === $transition = $stateMachine->getTransitionFromState($state)) {
            throw new \Exception('Invalid checkout flow configuration.');
        }

        switch ($transition) {
            case OrderCheckoutTransitions::SYLIUS_ADDRESSING:
                return $this->addressingAction($request, $order);
            break;
            case OrderCheckoutTransitions::SYLIUS_SHIPPING:
                return $this->shippingAction($request, $order);
            break;
            case OrderCheckoutTransitions::SYLIUS_PAYMENT:
                return $this->paymentAction($request, $order);
            break;
            case OrderCheckoutTransitions::SYLIUS_FINALIZE:
                return $this->finalizeAction($request, $order);
            break;
        }

        throw new \Exception('Could not process checkout API request.');
    }

    public function addressingAction(Request $request, OrderInterface $order)
    {
        if ($order->isEmpty()) {
            //return new Response('Order cannot be empty!', 400);
        }

        if ($request->isMethod('GET')) {
            return new Response('Method not allowed!', 405);
        }

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_INITIALIZE, $order);

        $form = $this->createCheckoutAddressingForm($order);

        if ($form->handleRequest($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_PRE_COMPLETE, $order);

            $stateMachine = $this->get('sm.factory')->get($order, OrderCheckoutTransitions::GRAPH);
            $stateMachine->apply(OrderCheckoutTransitions::SYLIUS_ADDRESSING);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_COMPLETE, $order);

            return $this->handleView($this->view($order));
        }

        return $this->handleView($this->view($form));
    }

    public function shippingAction(Request $request, OrderInterface $order)
    {
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);

        $form = $this->createCheckoutShippingForm($order);

        if ($request->isMethod('GET')) {
            $shipments = array();
            $form->handleRequest($request);

            foreach ($order->getShipments() as $key => $shipment) {
                $shipments[] = array(
                    'shipment' => $shipment,
                    'methods'  => $form['shipments'][$key]['method']->getConfig()->getOption('choice_list')->getChoices(),
                );
            }

            return $this->handleView($this->view($shipments));
        }

        if ($form->handleRequest($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, $order);

            $stateMachine = $this->get('sm.factory')->get($order, OrderCheckoutTransitions::GRAPH);
            $stateMachine->apply(OrderCheckoutTransitions::SYLIUS_SHIPPING);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_COMPLETE, $order);

            return $this->handleView($this->view($order));
        }

        return $this->handleView($this->view($form));
    }

    private function getOrderRepository()
    {
        return $this->get('sylius.repository.order');
    }

    private function findOrderOr404($id)
    {
        if (!$order = $this->getOrderRepository()->find($id)) {
            throw new NotFoundHttpException('Order does not exist.');
        }

        return $order;
    }

    private function createCheckoutAddressingForm(OrderInterface $order)
    {
        return $this->createApiForm('sylius_checkout_addressing', $order);
    }

    private function createCheckoutShippingForm(OrderInterface $order)
    {
        $zones = $this->getZoneMatcher()->matchAll($order->getShippingAddress());

        return $this->createApiForm('sylius_checkout_shipping', $order, array(
            'criteria' => array(
                'zone' => !empty($zones) ? array_map(function ($zone) {
                    return $zone->getId();
                }, $zones) : null,
                'enabled' => true,
            )
        ));
    }

    private function createApiForm($type, $value = null, array $options = array())
    {
        return $this->get('form.factory')->createNamed('', $type, $value, array_merge($options, array('csrf_protection' => false)));
    }

    /**
     * Get object manager.
     *
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->get('doctrine')->getManager();
    }

    /**
     * Get zone matcher.
     *
     * @return ZoneMatcherInterface
     */
    protected function getZoneMatcher()
    {
        return $this->get('sylius.zone_matcher');
    }

    /**
     * Is user logged in?
     *
     * @return Boolean
     */
    protected function isUserLoggedIn()
    {
        try {
            return $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * Dispatch event.
     *
     * @param string $name
     * @param Event  $event
     */
    protected function dispatchEvent($name, Event $event)
    {
        $this->get('event_dispatcher')->dispatch($name, $event);
    }

    /**
     * Dispatch checkout event.
     *
     * @param string         $name
     * @param OrderInterface $order
     */
    protected function dispatchCheckoutEvent($name, OrderInterface $order)
    {
        $this->dispatchEvent($name, new GenericEvent($order));
    }
}
