<?php

namespace spec\Sylius\Bundle\InventoryBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Stockable entity spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class Stockable extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Entity\Stockable');
    }

    function it_should_be_a_Sylius_stockable()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Model\StockableInterface');
    }

    function it_should_extend_Sylius_stockable_model()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Model\Stockable');
    }
}
