<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\InventoryBundle\Controller;

use Sylius\ResourceBundle\Controller\ResourceController;
use Sylius\Inventory\InventoryUnitTransitions;

class InventoryUnitController extends ResourceController
{
    protected $stateMachineGraph = InventoryUnitTransitions::GRAPH;
}
