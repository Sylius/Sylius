<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Report\Renderer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChartRenderer implements RendererInterface
{
    private $templating;

    public function __construct(EngineInterface $templating)
    {  
        $this->templating = $templating;
    }   

    public function render($data, $configuration)
    {
        var_dump($data);
        exit;

        $data = array(
            'report' => $data["report"],
            'values' => array(
                array('xAxis' => 'January','yAxis' => 20),
                array('xAxis' => 'February','yAxis' => 10),
                array('xAxis' => 'March','yAxis' => 25),
                array('xAxis' => 'April','yAxis' => 15),
                array('xAxis' => 'May', 'yAxis' => 5),
                array('xAxis' => 'June', 'yAxis' => 45),
                array('xAxis' => 'July', 'yAxis' => 70),
                array('xAxis' => 'August', 'yAxis' => 17),
                array('xAxis' => 'September', 'yAxis' => 40),
                array('xAxis' => 'October', 'yAxis' => 12),
                array('xAxis' => 'November', 'yAxis' => 47),
                array('xAxis' => 'December', 'yAxis' => 64)
            ),
            'labels' => array('Month', 'Users number')
        );

        return $this->templating->renderResponse(sprintf("SyliusReportBundle:Chart:%schartTemplate%s.html.twig", $configuration['type'], $configuration['template']), $data);
    }

    public function getType()
    {
        return 'chart';
    }
}