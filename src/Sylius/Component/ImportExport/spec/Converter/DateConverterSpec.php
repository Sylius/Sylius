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
}