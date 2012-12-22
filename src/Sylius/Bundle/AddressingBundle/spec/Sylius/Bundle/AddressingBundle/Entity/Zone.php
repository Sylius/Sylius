<?php

namespace spec\Sylius\Bundle\AddressingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Zone entity spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class Zone extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Entity\Zone');
    }

    function it_should_be_Sylius_zone()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ZoneInterface');
    }

    function it_should_extend_Sylius_zone_model()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Zone');
    }
}
