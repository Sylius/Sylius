<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Converter;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DateConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Converter\DateConverter');
    }

    function it_implements_date_converter_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Converter\DateConverterInterface');
    }

    function it_converts_date_to_string()
    {
        $date = new \DateTime('2012-07-08 11:14:15');
        $format = 'Y-m-d H:i:s';

        $this->toString($date, $format)->shouldReturn('2012-07-08 11:14:15');
    }

    function it_converts_string_to_dates()
    {
        $date = new \DateTime('2012-07-08 11:14:15');

        $this->toDateTime('2012-07-08 11:14:15', 'Y-m-d H:i:s')->shouldBeLike($date);
    }

    function it_throws_invalid_argument_exception_when_wrong_format_is_given()
    {
        $this->shouldThrow(new \InvalidArgumentException('Given format is invalid.'))->duringToDateTime('2012-07-08 11:14:15', 'INVALID');
    }
}