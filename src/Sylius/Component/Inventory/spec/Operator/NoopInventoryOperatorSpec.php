<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Operator;

use PhpSpec\ObjectBehavior;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class NoopInventoryOperatorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Operator\NoopInventoryOperator');
    }

    public function it_implements_Sylius_inventory_operator_interface()
    {
        $this->shouldImplement('Sylius\Component\Inventory\Operator\InventoryOperatorInterface');
    }
}
