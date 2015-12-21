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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class CountrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\Country');
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

    function it_returns_code_when_converted_to_string()
    {
        $this->setCode('VE');
        $this->__toString()->shouldReturn('VE');

        $this->setCode('CO');
        $this->__toString()->shouldReturn('CO');
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

    function it_initializes_administrative_areas_collection_by_default()
    {
        $this->getAdministrativeAreas()->shouldHaveType(Collection::class);
    }

    function it_has_no_administrative_areas_by_default()
    {
        $this->hasAdministrativeAreas()->shouldReturn(false);
    }

    function its_administrative_areas_are_mutable(Collection $administrativeAreas)
    {
        $this->setAdministrativeAreas($administrativeAreas);
        $this->getAdministrativeAreas()->shouldReturn($administrativeAreas);
    }

    function it_adds_administrative_area(AdministrativeAreaInterface $administrativeArea)
    {
        $this->addAdministrativeArea($administrativeArea);
        $this->hasAdministrativeArea($administrativeArea)->shouldReturn(true);
    }

    function it_removes_administrative_area(AdministrativeAreaInterface $administrativeArea)
    {
        $this->addAdministrativeArea($administrativeArea);
        $this->hasAdministrativeArea($administrativeArea)->shouldReturn(true);

        $this->removeAdministrativeArea($administrativeArea);
        $this->hasAdministrativeArea($administrativeArea)->shouldReturn(false);
    }

    function it_sets_country_on_added_administrative_area(AdministrativeAreaInterface $administrativeArea)
    {
        $administrativeArea->setCountry($this)->shouldBeCalled();
        $this->addAdministrativeArea($administrativeArea);
    }

    function it_unsets_country_on_removed_administrative_area(AdministrativeAreaInterface $administrativeArea)
    {
        $this->addAdministrativeArea($administrativeArea);
        $this->hasAdministrativeArea($administrativeArea)->shouldReturn(true);

        $administrativeArea->setCountry(null)->shouldBeCalled();

        $this->removeAdministrativeArea($administrativeArea);
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
    }
}
