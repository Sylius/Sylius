<?php

namespace spec\Sylius\Bundle\AddressingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Common address entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CommonAddress extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Entity\CommonAddress');
    }

    function it_should_be_sylius_common_address()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\CommonAddressInterface');
    }
}
