<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Operator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class NoopInventoryOperatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Operator\NoopInventoryOperator');
    }

    function it_implements_Sylius_inventory_operator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface');
    }
}
