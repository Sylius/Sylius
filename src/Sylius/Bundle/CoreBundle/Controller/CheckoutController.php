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
use Sylius\Component\Core\SyliusOrderEvents;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

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

        if ($request->isMethod('GET') && $order->isCompleted()) {
            return $this->handleView($this->view($order));
        }

        switch ($transition) {
            case OrderCheckoutTransitions::SYLIUS_ADDRESSING:
                return $this->addressingAction($request, $order);

            case OrderCheckoutTransitions::SYLIUS_SHIPPING:
                return $this->shippingAction($request, $order);

            case OrderCheckoutTransitions::SYLIUS_PAYMENT:
                return $this->paymentAction($request, $order);

            case OrderCheckoutTransitions::SYLIUS_FINALIZE:
                return $this->finalizeAction($request, $order);
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

        if ($form->submit($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_PRE_COMPLETE, $order);

            $stateMachine = $this->get('sm.factory')->get($order, OrderCheckoutTransitions::GRAPH);
            $stateMachine->apply(OrderCheckoutTransitions::SYLIUS_ADDRESSING);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_COMPLETE, $order);

            return $this->handleView($this->view($order));
        }

        return $this->handleView($this->view($form, 400));
    }

    public function shippingAction(Request $request, OrderInterface $order)
    {
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);

        $form = $this->createCheckoutShippingForm($order);

        if ($request->isMethod('GET')) {
            $shipments = array();
            $form->submit($request);

            foreach ($order->getShipments() as $key => $shipment) {
                $shipments[] = array(
                    'shipment' => $shipment,
                    'methods'  => $form['shipments'][$key]['method']->getConfig()->getOption('choice_list')->getChoices(),
                );
            }

            return $this->handleView($this->view($shipments));
        }

        if ($form->submit($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, $order);

            $stateMachine = $this->get('sm.factory')->get($order, OrderCheckoutTransitions::GRAPH);
            $stateMachine->apply(OrderCheckoutTransitions::SYLIUS_SHIPPING);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_COMPLETE, $order);

            return $this->handleView($this->view($order));
        }

        return $this->handleView($this->view($form, 400));
    }

    public function paymentAction(Request $request, OrderInterface $order)
    {
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_INITIALIZE, $order);

        $form = $this->createCheckoutPaymentForm($order);

        if ($request->isMethod('GET')) {
            $form->submit($request);

            $paymentInfo = array(
                'payment' => $order->getLastPayment(),
                'methods' => $form['paymentMethod']->getConfig()->getOption('choice_list')->getChoices(),
            );

            return $this->handleView($this->view($paymentInfo));
        }

        if ($form->submit($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_PRE_COMPLETE, $order);

            $stateMachine = $this->get('sm.factory')->get($order, OrderCheckoutTransitions::GRAPH);
            $stateMachine->apply(OrderCheckoutTransitions::SYLIUS_PAYMENT);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_COMPLETE, $order);

            return $this->handleView($this->view($order));
        }

        return $this->handleView($this->view($form, 400));
    }

    public function finalizeAction(Request $request, OrderInterface $order)
    {
        if ($request->isMethod('GET')) {
            return $this->handleView($this->view($order));
        }

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);

        $this->dispatchCheckoutEvent(SyliusOrderEvents::PRE_CREATE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_PRE_COMPLETE, $order);

        $this->get('sm.factory')->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CREATE, true);

        $stateMachine = $this->get('sm.factory')->get($order, OrderCheckoutTransitions::GRAPH);
        $stateMachine->apply(OrderCheckoutTransitions::SYLIUS_FINALIZE);

        $manager = $this->get('sylius.manager.order');
        $manager->persist($order);
        $manager->flush();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_COMPLETE, $order);
        $this->dispatchCheckoutEvent(SyliusOrderEvents::POST_CREATE, $order);

        return $this->handleView($this->view($order));
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

    private function createCheckoutPaymentForm(OrderInterface $order)
    {
        return $this->createApiForm('sylius_checkout_payment', $order);
    }

    private function createApiForm($type, $value = null, array $options = array())
    {
        return $this->get('form.factory')->createNamed('', $type, $value, array_merge($options, array('csrf_protection' => false)));
    }
}
