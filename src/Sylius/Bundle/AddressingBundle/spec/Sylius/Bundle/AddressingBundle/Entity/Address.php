<?php

namespace spec\Sylius\Bundle\AddressingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Address entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Address extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Entity\Address');
    }

    function it_should_be_Sylius_address()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\AddressInterface');
    }

    function it_should_extend_Sylius_address_model()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Address');
    }
}
