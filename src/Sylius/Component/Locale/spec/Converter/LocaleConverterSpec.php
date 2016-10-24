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
use Sylius\Component\Locale\Converter\LocaleConverter;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class LocaleConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleConverter::class);
    }

    function it_is_a_locale_converter()
    {
        $this->shouldImplement(LocaleConverterInterface::class);
    }

    function it_converts_locale_name_to_locale_code()
    {
        $this->convertNameToCode('German')->shouldReturn('de');
        $this->convertNameToCode('Norwegian')->shouldReturn('no');
        $this->convertNameToCode('Polish')->shouldReturn('pl');
    }

    function it_converts_locale_code_to_locale_name()
    {
        $this->convertCodeToName('de')->shouldReturn('German');
        $this->convertCodeToName('no')->shouldReturn('Norwegian');
        $this->convertCodeToName('pl')->shouldReturn('Polish');
    }

    function it_throws_invalid_argument_exception_if_cannot_convert_name_to_code()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('convertNameToCode', ['xyz']);
    }

    function it_throws_invalid_argument_exception_if_cannot_convert_code_to_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('convertCodeToName', ['xyz']);
    }
}
