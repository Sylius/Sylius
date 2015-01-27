<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Renderer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Report\Renderer\DefaultRenderers;

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
        if (null !== $data["data"]->getData()) {
            $data = array(
                'report' => $data["report"],
                'values' => $data["data"]->getData(),
                'labels' => array_keys($data["data"]->getData())
            );

            return $this->templating->renderResponse($configuration["template"], array('data' => $data, 'configuration' => $configuration));
        }
        return $this->templating->renderResponse("SyliusReportBundle::noDataTemplate.html.twig", array('report' => $data['report']));;
    }

    public function getType()
    {
        return DefaultRenderers::CHART;
    }
}