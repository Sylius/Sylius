<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\Renderer\DefaultRenderers;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class TableRendererSpec extends ObjectBehavior
{
    function let(EngineInterface $templating)
    {
        $this->beConstructedWith($templating);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Renderer\TableRenderer');
    }

    function it_should_implement_renderer_interface()
    {
        $this->shouldImplement('Sylius\Component\Report\Renderer\RendererInterface');
    }

    function it_renders_data_with_given_configuration(ReportInterface $report, Response $response, Data $reportData, $templating)
    {
        $reportData->getLabels()->willReturn(array('month', 'user_total'));
        $reportData->getData()->willReturn(array('month1' => '50', 'month2' => '40'));

        $renderData = array(
            'report' => $report,
            'values' => array('month1' => '50', 'month2' => '40'),
            'labels' => array('month', 'user_total'),
            'fields' => array('month1', 'month2'),
        );

        $report->getRendererConfiguration()->willReturn(array('template' => 'SyliusReportBundle:Table:default.html.twig'));

        $templating->renderResponse('SyliusReportBundle:Table:default.html.twig', array(
            'data' => $renderData,
            'configuration' => array('template' => 'SyliusReportBundle:Table:default.html.twig'),
        ))->willReturn($response);

        $this->render($report, $reportData)->shouldReturn($response);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn(DefaultRenderers::TABLE);
    }
}
