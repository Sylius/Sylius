<?php

namespace spec\Sylius\Bundle\AddressingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Country entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Country extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Entity\Country');
    }

    function it_should_be_Sylius_country()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\CountryInterface');
    }

    function it_should_extend_Sylius_country_model()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Country');
    }
}
