<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ProvinceInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CountrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\Country');
    }

    function it_implements_Sylius_country_interface()
    {
        $this->shouldImplement('Sylius\Component\Addressing\Model\CountryInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('United States');
        $this->getName()->shouldReturn('United States');
    }

    function it_has_no_iso_name_by_default()
    {
        $this->getIsoName()->shouldReturn(null);
    }

    function its_iso_name_is_mutable()
    {
        $this->setIsoName('MX');
        $this->getIsoName()->shouldReturn('MX');
    }

    function it_initializes_provinces_collection_by_default()
    {
        $this->getProvinces()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_has_no_provinces_by_default()
    {
        $this->hasProvinces()->shouldReturn(false);
    }

    function its_provinces_are_mutable(Collection $provinces)
    {
        $this->setProvinces($provinces);
        $this->getProvinces()->shouldReturn($provinces);
    }

    function it_adds_province(ProvinceInterface $province)
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);
    }

    function it_removes_province(ProvinceInterface $province)
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);

        $this->removeProvince($province);
        $this->hasProvince($province)->shouldReturn(false);
    }

    function it_sets_country_on_added_province(ProvinceInterface $province)
    {
        $province->setCountry($this)->shouldBeCalled();

        $this->addProvince($province);
    }

    function it_unsets_country_on_removed_province(ProvinceInterface $province)
    {
        $this->addProvince($province);
        $this->hasProvince($province)->shouldReturn(true);

        $province->setCountry(null)->shouldBeCalled();

        $this->removeProvince($province);
    }

    function it_has_fluent_interface(
        ProvinceInterface $province,
        Collection $provinces
    )
    {
        $this->setName('Poland')->shouldReturn($this);
        $this->setIsoName('PL')->shouldReturn($this);

        $this->setProvinces($provinces)->shouldReturn($this);
        $this->addProvince($province)->shouldReturn($this);
        $this->removeProvince($province)->shouldReturn($this);
    }
}
