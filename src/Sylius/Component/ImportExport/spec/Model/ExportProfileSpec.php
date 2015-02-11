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

use PhpSpec\ObjectBehavior;

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

    public function it_is_profile_object()
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

    public function it_has_Reader_configuration()
    {
        $readerConfiguration = array('config1' => 'First field of configuration', 'config2' => 'Second field of configuration');
        $this->setReaderConfiguration($readerConfiguration);
        $this->getReaderConfiguration()->shouldReturn($readerConfiguration);
    }
}
