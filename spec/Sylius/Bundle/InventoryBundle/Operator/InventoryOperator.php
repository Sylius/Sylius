<?php

namespace spec\Sylius\Bundle\InventoryBundle\Operator;

use PHPSpec2\ObjectBehavior;

/**
 * Inventory operator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryOperator extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param Doctrine\Common\Persistence\ObjectRepository $repository
     */
    function let($manager, $repository)
    {
        $this->beConstructedWith($manager, $repository, true);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Operator\InventoryOperator');
    }

    function it_should_be_Sylius_inventory_operator()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface');
    }
}
