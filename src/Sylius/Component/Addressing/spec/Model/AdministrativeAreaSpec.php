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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AdministrativeAreaSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\AdministrativeArea');
    }

    function it_implements_administrative_area_interface()
    {
        $this->shouldImplement(AdministrativeAreaInterface::class);
    }

    function it_implements_code_aware_interface()
    {
        $this->shouldImplement(CodeAwareInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('DUB');
        $this->getCode()->shouldReturn('DUB');
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Dublin');
        $this->getName()->shouldReturn('Dublin');
    }

    function it_does_not_belong_to_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    function it_allows_to_attach_itself_to_a_country(CountryInterface $country)
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);
    }
}
