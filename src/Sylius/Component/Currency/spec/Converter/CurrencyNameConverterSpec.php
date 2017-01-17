<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Converter\CurrencyNameConverter;
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CurrencyNameConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyNameConverter::class);
    }

    function it_implements_a_currency_name_converter_interface()
    {
        $this->shouldImplement(CurrencyNameConverterInterface::class);
    }

    function it_converts_an_english_currency_name_to_code_by_default()
    {
        $this->convertToCode('Euro')->shouldReturn('EUR');
    }

    function it_converts_a_name_to_a_code_for_given_locale()
    {
        $this->convertToCode('rupia indyjska', 'pl')->shouldReturn('INR');
    }

    function it_throws_an_invalid_argument_exception_when_currency_not_exists()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('convertToCode', ['Meuro']);
    }
}
