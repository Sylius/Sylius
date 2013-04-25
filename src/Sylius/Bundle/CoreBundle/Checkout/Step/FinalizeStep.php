<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Final checkout step.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FinalizeStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->createOrder($context);

        return $this->renderStep($context, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $order = $this->createOrder($context);

        $this->saveOrder($order);
        $this->getCartProvider()->abandonCart();

        $translator = $this->get('translator');
        $this->get('session')->getFlashBag()->add('success', $translator->trans('sylius.checkout.success', array(), 'flashes'));

        return $this->complete();
    }

    private function renderStep(ProcessContextInterface $context, OrderInterface $order)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:finalize.html.twig', array(
            'context' => $context,
            'order'   => $order
        ));
    }

    /**
     * Create order based on the checkout context.
     *
     * @param ProcessContextInterface $context
     *
     * @return OrderInterface
     */
    private function createOrder(ProcessContextInterface $context)
    {
        $orderBuilder = $this->getOrderBuilder();
        $orderBuilder->create();

        foreach ($this->getCurrentCart()->getItems() as $item) {
            $orderBuilder->add($item->getVariant(), $item->getUnitPrice(), $item->getQuantity());
        }

        $order = $orderBuilder->getOrder();

        $shippingAddress = $this->getAddress($context->getStorage()->get('shipping_address'));
        $billingAddress = $this->getAddress($context->getStorage()->get('billing_address'));

        $order->setshippingAddress($shippingAddress);
        $order->setBillingAddress($billingAddress);

        $this
            ->getInventoryUnitsFactory()
            ->createInventoryUnits($order)
        ;

        $shippingMethod = $this->getShippingMethod($context->getStorage()->get('shipping_method'));

        $this
            ->getShipmentFactory()
            ->createShipment($order, $shippingMethod)
        ;

        $this->get('event_dispatcher')->dispatch('sylius.order.pre_create', new GenericEvent($order));

        $order->calculateTotal();

        return $order;
    }

    /**
     * Save given order.
     *
     * @param OrderInterface $order
     */
    protected function saveOrder(OrderInterface $order)
    {
        $manager = $this->get('sylius.manager.order');

        $manager->persist($order);
        $manager->flush($order);

        $this->get('event_dispatcher')->dispatch('sylius.order.post_create', new GenericEvent($order));
    }

    private function getOrderBuilder()
    {
        return $this->get('sylius.builder.order');
    }

    private function getInventoryUnitsFactory()
    {
        return $this->get('sylius.order_processing.inventory_units_factory');
    }

    private function getShipmentFactory()
    {
        return $this->get('sylius.order_processing.shipment_factory');
    }

    private function getShippingMethod($id)
    {
        $repository = $this->get('sylius.repository.shipping_method');

        return $repository->find($id);
    }

    private function createNewShipment()
    {
        return $this->get('sylius.repository.shipment')->createNew();
    }
}
