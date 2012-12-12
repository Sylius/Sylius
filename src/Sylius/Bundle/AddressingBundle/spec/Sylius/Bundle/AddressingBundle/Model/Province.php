<?php

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Province model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Province extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Province');
    }

    function it_should_be_Sylius_province()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ProvinceInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Texas');
        $this->getName()->shouldReturn('Texas');
    }

    function it_should_not_belong_to_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     */
    function it_should_defining_the_country($country)
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);
    }
}
