<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\Renderer\DefaultRenderers;
use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ChartRendererSpec extends ObjectBehavior
{
    function let(EngineInterface $templating)
    {
        $this->beConstructedWith($templating);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Renderer\ChartRenderer');
    }

    function it_should_implement_renderer_interface()
    {
        $this->shouldImplement(RendererInterface::class);
    }

    function it_renders_data_with_given_configuration(ReportInterface $report, Data $reportData, $templating)
    {
        $reportData->getData()->willReturn(['month1' => '50', 'month2' => '40']);

        $renderData = [
            'report' => $report,
            'values' => ['month1' => '50', 'month2' => '40'],
            'labels' => ['month1', 'month2'],
        ];

        $report->getRendererConfiguration()->willReturn(['template' => 'SyliusReportBundle:Chart:default.html.twig']);

        $templating->render('SyliusReportBundle:Chart:default.html.twig', [
            'data' => $renderData,
            'configuration' => ['template' => 'SyliusReportBundle:Chart:default.html.twig'],
        ])->willReturn('<div>Chart Report</div>');

        $this->render($report, $reportData)->shouldReturn('<div>Chart Report</div>');
    }

    function it_is_a_chart_type()
    {
        $this->getType()->shouldReturn(DefaultRenderers::CHART);
    }
}
