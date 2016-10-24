<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Converter\CountryNameConverter;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CountryNameConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CountryNameConverter::class);
    }

    function it_implements_country_name_to_code_converter_interface()
    {
        $this->shouldImplement(CountryNameConverterInterface::class);
    }

    function it_converts_english_country_name_to_codes_by_default()
    {
        $this->convertToCode('Australia')->shouldReturn('AU');
        $this->convertToCode('China')->shouldReturn('CN');
        $this->convertToCode('France')->shouldReturn('FR');
    }

    function it_converts_country_name_to_codes_for_given_locale()
    {
        $this->convertToCode('Niemcy', 'pl')->shouldReturn('DE');
        $this->convertToCode('Chine', 'fr')->shouldReturn('CN');
        $this->convertToCode('Francia', 'es')->shouldReturn('FR');
    }

    function it_throws_an_exception_if_country_name_cannot_be_converted_to_code()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('convertToCode', ['Atlantis']);
    }
}
