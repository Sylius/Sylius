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
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Provider\ProvinceNamingProviderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ProvinceNamingExtensionSpec extends ObjectBehavior
{
    function let(ProvinceNamingProviderInterface $provinceNamingProvider)
    {
        $this->beConstructedWith($provinceNamingProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Twig\ProvinceNamingExtension');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_gets_province_name_by_its_code(ProvinceNamingProviderInterface $provinceNamingProvider, AddressInterface $address)
    {
        $provinceNamingProvider->getName($address)->willReturn('Ulster');

        $this->getProvinceName($address)->shouldReturn('Ulster');
    }

    function it_gets_province_abbreviation_by_its_code(ProvinceNamingProviderInterface $provinceNamingProvider, AddressInterface $address)
    {
        $provinceNamingProvider->getAbbreviation($address)->willReturn('ULS');

        $this->getProvinceAbbreviation($address)->shouldReturn('ULS');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_province_naming');
    }
}
