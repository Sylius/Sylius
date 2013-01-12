<?php

namespace spec\Sylius\Bundle\InventoryBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Inventory unit entity spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnit extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Entity\InventoryUnit');
    }

    function it_should_be_a_Sylius_inventory_unit()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface');
    }

    function it_should_extend_Sylius_inventory_unit_model()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Model\InventoryUnit');
    }
}
