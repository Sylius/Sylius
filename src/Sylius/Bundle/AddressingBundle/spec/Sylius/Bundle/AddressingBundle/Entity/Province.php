<?php

namespace spec\Sylius\Bundle\AddressingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Province entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Province extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Entity\Province');
    }

    function it_should_be_Sylius_province()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ProvinceInterface');
    }

    function it_should_extend_Sylius_province_model()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Province');
    }
}
