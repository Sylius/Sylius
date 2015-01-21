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
use Sylius\Component\Report\Model\Report;
use Symfony\Component\HttpFoundation\Response;
use Prophecy\Argument;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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

    function it_renders_data_with_given_configuration(Report $report, Response $response, $templating)
    {
        $data = array(
            'report' => $report,
            'data' => array(
                'column_name' => array('column1', 'column2'),
                'Month1' => '50',
                'Month2' => '20'
            )
        );
        $tableData = array(
            'report' => $report,
            'data' => array(
                'Month1' => '50',
                'Month2' => '20'
            )
        );
        $renderData = array(
            'report' => $tableData["report"],
            'values' => $tableData["data"],
            'labels' => $data["data"]["column_name"],
            'fields' => array_keys($tableData["data"])
        );
        $configuration = array('template' => 'SyliusReportBundle:Table:default.html.twig');

        $templating->renderResponse($configuration["template"], array('data' => $renderData, 'configuration' => $configuration))->willReturn($response);
        $this->render($data, $configuration)->shouldReturn($response);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('table');
    }
}