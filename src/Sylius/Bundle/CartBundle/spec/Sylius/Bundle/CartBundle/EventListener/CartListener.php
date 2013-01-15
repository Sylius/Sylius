<?php

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use PHPSpec2\ObjectBehavior;

/**
 * Cart listener spec.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartListener extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param Sylius\Bundle\CartBundle\Operator\InventoryOperatorInterface $validator
     */
    function let($manager, $validator)
    {
        $this->beConstructedWith($manager, $validator);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\CartListener');
    }
}
