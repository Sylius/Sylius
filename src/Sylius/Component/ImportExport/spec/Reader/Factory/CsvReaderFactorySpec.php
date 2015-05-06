<?php

namespace spec\Sylius\Component\ImportExport\Reader\Factory;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CsvReaderFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Reader\Factory\CsvReaderFactory');
    }

    function it_implements_csv_reader_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Reader\Factory\CsvReaderFactoryInterface');
    }
}