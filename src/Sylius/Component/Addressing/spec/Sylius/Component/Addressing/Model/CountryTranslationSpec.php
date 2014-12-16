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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class CountryTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\CountryTranslation');
    }

    function it_implements_Sylius_country_interface()
    {
        $this->shouldImplement('Sylius\Component\Addressing\Model\CountryTranslationInterface');
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

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('Spain');
        $this->__toString()->shouldReturn('Spain');
    }

    function it_has_fluent_interface()
    {
        $this->setName('Poland')->shouldReturn($this);
    }
}
