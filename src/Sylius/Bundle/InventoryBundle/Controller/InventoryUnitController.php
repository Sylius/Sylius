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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Inventory\InventoryUnitTransitions;
use Symfony\Component\HttpFoundation\Request;

class InventoryUnitController extends ResourceController
{
    public function updateStateAction(Request $request, $transition)
    {
        $unit = $this->findOr404($request);

        $stateMachine = $this->get('finite.factory')->get($unit, InventoryUnitTransitions::GRAPH);
        if (!$stateMachine->can($transition)) {
            $this->flashHelper->setFlash('error', 'sylius.inventory_unit.transition_fail');

            return $this->redirectHandler->redirectToReferer();
        }

        $stateMachine->apply($transition);

        $this->domainManager->update($unit);

        $this->flashHelper->setFlash('success', 'sylius.inventory_unit.'.$transition);

        return $this->redirectHandler->redirectToReferer();
    }
}
