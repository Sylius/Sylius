<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Shipping\SyliusShipmentEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

class ShipmentController extends ResourceController
{
    public function shipAction(Request $request)
    {
        $shipment = $this->findOr404($request);
        $form = $this->createForm('sylius_shipment_tracking', $shipment);

        if ($form->submit($request)->isValid()) {
            $this->get('event_dispatcher')->dispatch(SyliusShipmentEvents::PRE_SHIP, new GenericEvent($shipment));

            $this->domainManager->update($shipment);

            $this->get('event_dispatcher')->dispatch(SyliusShipmentEvents::POST_SHIP, new GenericEvent($shipment));

            $this->flashHelper->setFlash('success', 'sylius.shipment.ship.success');

            return $this->redirectHandler->redirectTo($shipment);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('show.html'))
            ->setTemplateVar($this->config->getResourceName())
            ->setData(array(
                'shipment'               => $shipment,
                'shipment_tracking_form' => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }
}
