<?php

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Country model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Country extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Country');
    }

    function it_should_be_Sylius_country()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\CountryInterface');
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
        $this->setName('United States');
        $this->getName()->shouldReturn('United States');
    }

    function it_should_not_have_iso_name_by_default()
    {
        $this->getIsoName()->shouldReturn(null);
    }

    function its_iso_name_should_be_mutable()
    {
        $this->setIsoName('MX');
        $this->getIsoName()->shouldReturn('MX');
    }

    function it_should_initialize_provinces_collection_by_default()
    {
        $this->getProvinces()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_should_have_no_provinces_by_default()
    {
        $this->hasProvinces()->shouldReturn(false);
    }

    /**
     * @param Doctrine\Common\Collections\Collection $provinces
     */
    function its_provinces_should_be_mutable($provinces)
    {
        $this->setProvinces($provinces);
        $this->getProvinces()->shouldReturn($provinces);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_should_add_province_properly($province)
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_should_remove_province_properly($province)
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);

        $this->removeProvince($province);
        $this->hasProvince($province)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_should_define_country_on_added_province($province)
    {
        $province->setCountry($this)->shouldBeCalled();

        $this->addProvince($province);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_should_detach_country_on_removed_province($province)
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);

        $province->setCountry(null)->shouldBeCalled();

        $this->removeProvince($province);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     * @param Doctrine\Common\Collections\Collection $provinces
     */
    function it_should_have_fluid_interface($province, $provinces)
    {
        $this->setName('Poland')->shouldReturn($this);
        $this->setIsoName('PL')->shouldReturn($this);

        $this->setProvinces($provinces)->shouldReturn($this);
        $this->addProvince($province)->shouldReturn($this);
        $this->removeProvince($province)->shouldReturn($this);
    }
}
