<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\ImportExport\Model\ExportJobInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ExportProfileSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\ExportProfile');
    }

    public function it_implements_import_profile_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Model\ExportProfileInterface');
    }

    public function it_implements_profile_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Model\ProfileInterface');
    }

    public function it_extends_profile()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\Profile');
    }

    public function it_has_writer()
    {
        $this->setWriter('testWriter');
        $this->getWriter()->shouldReturn('testWriter');
    }

    public function it_has_writer_configuration()
    {
        $writerConfiguration = array('config1' => 'First field of configuration', 'config2' => 'Second field of configuration');
        $this->setWriterConfiguration($writerConfiguration);
        $this->getWriterConfiguration()->shouldReturn($writerConfiguration);
    }

    public function it_has_name()
    {
        $this->setName("testExportProfile");
        $this->getName()->shouldReturn('testExportProfile');
    }

    public function it_has_code()
    {
        $this->setCode('testCode');
        $this->getCode()->shouldReturn('testCode');
    }

    public function it_has_description()
    {
        $this->setDescription('testDescription');
        $this->getDescription()->shouldReturn('testDescription');
    }

    public function it_has_reader()
    {
        $this->setReader('testReader');
        $this->getReader()->shouldReturn('testReader');
    }

    public function it_has_reader_configuration()
    {
        $readerConfiguration = array('config1' => 'First field of configuration', 'config2' => 'Second field of configuration');
        $this->setReaderConfiguration($readerConfiguration);
        $this->getReaderConfiguration()->shouldReturn($readerConfiguration);
    }

    public function it_has_jobs(Collection $jobsCollection)
    {
        $this->setJobs($jobsCollection);
        $this->getJobs()->shouldReturn($jobsCollection);
    }

    public function it_adds_job(ExportJobInterface $exportJob)
    {
        $this->addJob($exportJob);
        $this->shouldHaveJob($exportJob);
    }

    public function it_counts_job(ExportJobInterface $exportJob)
    {
        $this->addJob($exportJob);
        $this->countJobs()->shouldReturn(1);
    }

    public function it_clears_job(ExportJobInterface $exportJob)
    {
        $this->addJob($exportJob);
        $this->clearJobs();
        $this->countJobs()->shouldReturn(0);
    }

    public function it_removes_job(ExportJobInterface $exportJob)
    {
        $this->addJob($exportJob);
        $this->removeJob($exportJob);
        $this->shouldNotHaveJob($exportJob);
    }
}
