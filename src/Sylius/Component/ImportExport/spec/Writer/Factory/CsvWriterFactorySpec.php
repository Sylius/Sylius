<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Writer\Factory;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CsvWriterFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Writer\Factory\CsvWriterFactory');
    }

    function it_implements_csv_writer_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\Factory\CsvWriterFactoryInterface');
    }

    function it_throws_invalid_argument_exception_if_any_argument_is_not_set()
    {
        $this->shouldThrow(new \InvalidArgumentException('The fields: file, delimiter, enclosure has to be set'))->duringCreate(array(
            'headers' => 'headers',
            'delimiter' => 'delimiter',
            'enclosure' => 'enclosure',
        ));
        $this->shouldThrow(new \InvalidArgumentException('The fields: file, delimiter, enclosure has to be set'))->duringCreate(array(
            'file' => 'file',
            'headers' => 'headers',
            'enclosure' => 'enclosure',
        ));
        $this->shouldThrow(new \InvalidArgumentException('The fields: file, delimiter, enclosure has to be set'))->duringCreate(array(
            'file' => 'file',
            'headers' => 'headers',
            'delimiter' => 'delimiter',
        ));
    }
}