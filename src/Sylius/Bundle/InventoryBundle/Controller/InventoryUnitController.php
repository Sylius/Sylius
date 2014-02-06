<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\InventoryBundle\SyliusInventoryUnitEvents;

class InventoryUnitController extends ResourceController
{
    public function updateStateAction(Request $request, $state)
    {
        $unit = $this->findOr404($request);

        $this->get('event_dispatcher')->dispatch(
            SyliusInventoryUnitEvents::PRE_STATE_CHANGE,
            new GenericEvent($unit, array('state' => $state))
        );

        $this->domainManager->update($unit);

        $this->get('event_dispatcher')->dispatch(
            SyliusInventoryUnitEvents::POST_STATE_CHANGE,
            new GenericEvent($unit, array('state' => $state))
        );

        $this->flashHelper->setFlash('success', 'sylius.inventory_unit.'.$state);

        return $this->redirectHandler->redirectToReferer();
    }
}
