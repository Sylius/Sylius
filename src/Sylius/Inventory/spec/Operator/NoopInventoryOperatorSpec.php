<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Inventory\Operator;

use PhpSpec\ObjectBehavior;
use Sylius\Inventory\Operator\InventoryOperatorInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class NoopInventoryOperatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Inventory\Operator\NoopInventoryOperator');
    }

    function it_implements_Sylius_inventory_operator_interface()
    {
        $this->shouldImplement(InventoryOperatorInterface::class);
    }
}
