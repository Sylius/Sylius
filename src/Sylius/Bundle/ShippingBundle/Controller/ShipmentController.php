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
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\HttpFoundation\Request;

class ShipmentController extends ResourceController
{
    public function shipAction(Request $request)
    {
        $shipment = $this->findOr404($request);
        $form = $this->createForm('sylius_shipment_tracking', $shipment);

        if ($form->submit($request)->isValid()) {
            $this
                ->get('sm.factory')
                ->get($shipment, ShipmentTransitions::GRAPH)
                ->apply(ShipmentTransitions::SYLIUS_SHIP)
            ;

            $this->domainManager->update($shipment);

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
