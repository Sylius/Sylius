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

namespace spec\Sylius\Bundle\AddressingBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;

final class CountryNameExtensionSpec extends ObjectBehavior
{
    function it_is_a_twig_extension(): void
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_translates_country_iso_code_into_name(): void
    {
        $this->translateCountryIsoCode('IE')->shouldReturn('Ireland');
    }

    function it_translates_country_into_name(CountryInterface $country): void
    {
        $country->getCode()->willReturn('IE');

        $this->translateCountryIsoCode($country)->shouldReturn('Ireland');
    }

    function it_translates_country_code_to_name_according_to_locale(): void
    {
        $this->translateCountryIsoCode('IE', 'es')->shouldReturn('Irlanda');
    }
}
