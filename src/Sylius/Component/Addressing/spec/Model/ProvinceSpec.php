<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Addressing\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\Province;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;

final class ProvinceSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(Province::class);
    }

    function it_implements_Sylius_country_province_interface(): void
    {
        $this->shouldImplement(ProvinceInterface::class);
    }

    function it_implements_code_aware_interface(): void
    {
        $this->shouldImplement(CodeAwareInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('US-TX');
        $this->getCode()->shouldReturn('US-TX');
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Texas');
        $this->getName()->shouldReturn('Texas');
    }

    function it_has_no_abbreviation_by_default(): void
    {
        $this->getAbbreviation()->shouldReturn(null);
    }

    function its_abbreviation_is_mutable(): void
    {
        $this->setAbbreviation('TEX');
        $this->getAbbreviation()->shouldReturn('TEX');
    }

    function it_does_not_belong_to_country_by_default(): void
    {
        $this->getCountry()->shouldReturn(null);
    }

    function it_allows_to_attach_itself_to_a_country(CountryInterface $country): void
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);
    }
}
