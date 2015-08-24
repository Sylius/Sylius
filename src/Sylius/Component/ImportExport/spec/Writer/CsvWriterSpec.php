<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Writer;

use EasyCSV\Writer;
use Gaufrette\Filesystem;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Model\ProfileInterface;
use Sylius\Component\ImportExport\Writer\Factory\CsvWriterFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvWriterSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, CsvWriterFactoryInterface $csvWriterFactory)
    {
        $this->beConstructedWith($filesystem, $csvWriterFactory);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Writer\CsvWriter');
    }

    function it_implements_exporter_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('csv');
    }

    function it_has_result_code()
    {
        $this->getResultCode()->shouldReturn(0);
    }

    function it_writes_data_to_csv(
        $csvWriterFactory,
        LoggerInterface $logger,
        Writer $writer
    ) {
        $data = array(array('header' => 'data1'), array('header' => 'data2'));

        $csvWriterFactory->create(array())->willReturn($writer);

        $writer->writeFromArray(array(array('header' => 'data1'), array('header' => 'data2')))->shouldBeCalled();
        $writer->writeRow(array('header'))->shouldBeCalled();

        $this->write($data, $config = array(), $logger);
    }

    function it_finalize_job(
        \DateTime $date,
        JobInterface $exportJob,
        ProfileInterface $profile
    ) {
        $exportJob->getProfile()->willReturn($profile);
        $exportJob->getStartTime()->willReturn($date);
        $date->format('Y_m_d_H_i_s')->willReturn('2015_06_30_16_30_00');
        $profile->getId()->willReturn(1);

        $config = array(
            'file' => '/tmp'
        );

        $exportJob->addMetadata(array(
            'file_path' => '/tmp',
            'result_code' => 0,
        ))->shouldBeCalled();

        $this->finalize($exportJob, $config);
    }
}
