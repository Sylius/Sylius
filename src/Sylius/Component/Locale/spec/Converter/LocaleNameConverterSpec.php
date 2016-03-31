<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Locale\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Converter\LocaleNameConverter;
use Sylius\Component\Locale\Converter\LocaleNameConverterInterface;

/**
 * @mixin LocaleNameConverter
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class LocaleNameConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Locale\Converter\LocaleNameConverter');
    }

    function it_implements_converter_interface()
    {
        $this->shouldImplement(LocaleNameConverterInterface::class);
    }

    function it_converts_locale_name_to_respondent_code()
    {
        $this->convertToCode('German')->shouldReturn('de');
        $this->convertToCode('Norwegian')->shouldReturn('no');
        $this->convertToCode('Polish')->shouldReturn('pl');
    }

    function it_throws_invalid_argument_exception_if_cannot_convert_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('convertToCode', ['xyz']);
    }
}
