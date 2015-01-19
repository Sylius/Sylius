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
        $data = array(
            'values' => array(
                array('month' => 'January','newUsers' => 20),
                array('month' => 'February','newUsers' => 10),
                array('month' => 'March','newUsers' => 25),
                array('month' => 'April','newUsers' =>15)
            ),
            'labels' => array('Month', 'Users number')
        );

        $configuration = array('template' => 0, 'type' => 'bar');

        return $this->templating->renderResponse(sprintf("SyliusReportBundle:Chart:%schartTemplate%s.html.twig", $configuration['type'], $configuration['template']), $data);
    }

    public function getType()
    {
        return 'chart';
    }
}