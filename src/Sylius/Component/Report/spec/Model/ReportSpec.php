<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Report\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReportSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\Model\Report');
    }

    public function it_implements_report_interface()
    {
        $this->shouldImplement('Sylius\Component\Report\Model\ReportInterface');
    }

    public function it_has_name()
    {
        $this->setName('testName');
        $this->getName()->shouldReturn('testName');
    }

    public function it_has_description()
    {
        $this->setDescription('Test description for Report spec');
        $this->getDescription()->shouldReturn('Test description for Report spec');
    }

    public function it_has_data_fetcher()
    {
        $this->setDataFetcher('testFetcher');
        $this->getDataFetcher()->shouldReturn('testFetcher');
    }

    public function it_has_data_fetcher_configuration()
    {
        $this->setDataFetcherConfiguration(array());
        $this->getDataFetcherConfiguration()->shouldReturn(array());
    }

    public function it_has_renderer()
    {
        $this->setRenderer('testRenderer');
        $this->getRenderer()->shouldReturn('testRenderer');
    }

    public function it_has_renderer_configuration()
    {
        $this->setRendererConfiguration(array());
        $this->getRendererConfiguration()->shouldReturn(array());
    }
}
