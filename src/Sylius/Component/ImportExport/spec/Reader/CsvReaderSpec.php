<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Reader;

use EasyCSV\Reader;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Reader\Factory\CsvReaderFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CsvReaderSpec extends ObjectBehavior
{
    function let(CsvReaderFactoryInterface $csvReaderFactory)
    {
        $this->beConstructedWith($csvReaderFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Reader\CsvReader');
    }

    function it_implements_reader_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Reader\ReaderInterface');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('csv');
    }

    function it_has_result_code()
    {
        $this->getResultCode()->shouldReturn(0);
    }

    function it_reads_data_for_rows_greater_or_equal_to_batch_size(
        $csvReaderFactory,
        LoggerInterface $logger,
        Reader $reader
    ) {
        $config = array(
            'reader' => 'config',
            'batch' => 2,
        );
        $csvReaderFactory->create($config)->willReturn($reader);
        $reader->getRow()->willReturn('response1','response2','response3',false);

        $this->read($config, $logger)->shouldReturn(array('response1','response2'));
    }

    function it_reads_data_(
        $csvReaderFactory,
        LoggerInterface $logger,
        Reader $reader
    ) {
        $config = array(
            'reader' => 'config',
            'batch' => 2,
        );

        $csvReaderFactory->create($config)->willReturn($reader);
        $reader->getRow()->willReturn('response3',false);

        $this->read($config, $logger)->shouldReturn(array('response3'));
    }

    function it_finalize_job(JobInterface $job)
    {
        $job->addMetadata(array('result_code' => 0));

        $this->finalize($job);
    }
}
