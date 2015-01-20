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
            'report' => $data["report"],
            'values' => $data["data"],
            'labels' => array_keys($data["data"])
        );

        var_dump($data);
        exit;

        return $this->templating->renderResponse($configuration["template"], array('data' => $data, 'configuration' => $configuration));
    }

    public function getType()
    {
        return 'chart';
    }
}