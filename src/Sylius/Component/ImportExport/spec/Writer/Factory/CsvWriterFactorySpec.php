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
}