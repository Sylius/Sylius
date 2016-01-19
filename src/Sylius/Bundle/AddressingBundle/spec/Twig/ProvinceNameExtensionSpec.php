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
use Sylius\Component\Addressing\Provider\ProvinceNameProviderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceNameExtensionSpec extends ObjectBehavior
{
    function let(ProvinceNameProviderInterface $provinceNameProvider)
    {
        $this->beConstructedWith($provinceNameProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Twig\ProvinceNameExtension');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_gets_province_name_by_its_code(ProvinceNameProviderInterface $provinceNameProvider)
    {
        $provinceNameProvider->get('IE-UL')->willReturn('Ulster');

        $this->getProvinceName('IE-UL')->shouldReturn('Ulster');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_province_name');
    }
}
