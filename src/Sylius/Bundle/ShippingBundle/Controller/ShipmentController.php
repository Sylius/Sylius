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

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\HttpFoundation\Request;

class ShipmentController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function shipAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $shipment = $this->findOr404($configuration);

        $form = $this->createForm('sylius_shipment_ship', $shipment);

        if ($form->submit($request)->isValid()) {
            $this
                ->get('sm.factory')
                ->get($shipment, ShipmentTransitions::GRAPH)
                ->apply(ShipmentTransitions::SYLIUS_SHIP)
            ;

            $this->manager->flush();

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $shipment);

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $shipment,
                'shipment' => $shipment,
                'shipment_tracking_form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(ResourceActions::SHOW))
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
