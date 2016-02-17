<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CountryNameExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Twig\CountryNameExtension');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_translates_country_iso_code_into_name()
    {
        $this->translateCountryIsoCode('IE')->shouldReturn('Ireland');
    }

    function it_translates_country_into_name(CountryInterface $country)
    {
        $country->getCode()->willReturn('IE');

        $this->translateCountryIsoCode($country)->shouldReturn('Ireland');
    }

    function it_translates_country_code_to_name_according_to_locale()
    {
        $this->translateCountryIsoCode('IE', 'es')->shouldReturn('Irlanda');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_country_name');
    }
}
