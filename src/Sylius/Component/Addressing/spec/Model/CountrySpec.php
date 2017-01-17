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
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
final class CountrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Country::class);
    }

    function it_implements_Sylius_country_interface()
    {
        $this->shouldImplement(CountryInterface::class);
    }

    function it_is_toggleable()
    {
        $this->shouldImplement(ToggleableInterface::class);
    }

    function it_implements_code_aware_interface()
    {
        $this->shouldImplement(CodeAwareInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setCode('VE');
        $this->__toString()->shouldReturn('Venezuela');
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('MX');
        $this->getCode()->shouldReturn('MX');
    }

    function it_initializes_provinces_collection_by_default()
    {
        $this->getProvinces()->shouldHaveType(Collection::class);
    }

    function it_has_no_provinces_by_default()
    {
        $this->hasProvinces()->shouldReturn(false);
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

    function it_is_enabled_by_default()
    {
        $this->isEnabled()->shouldReturn(true);
    }

    function it_can_be_disabled()
    {
        $this->disable();
        $this->isEnabled()->shouldReturn(false);
    }

    function it_can_be_enabled()
    {
        $this->disable();
        $this->isEnabled()->shouldReturn(false);

        $this->enable();
        $this->isEnabled()->shouldReturn(true);
    }

    function it_can_set_enabled_value()
    {
        $this->setEnabled(false);
        $this->isEnabled()->shouldReturn(false);

        $this->setEnabled(true);
        $this->isEnabled()->shouldReturn(true);

        $this->setEnabled(false);
        $this->isEnabled()->shouldReturn(false);
    }
}
