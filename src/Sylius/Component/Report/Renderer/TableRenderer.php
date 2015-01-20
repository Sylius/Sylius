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

use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TableRenderer implements RendererInterface
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
            'values' => array(
                array('month' => 'January','newUsers' => 20),
                array('month' => 'February','newUsers' => 10),
                array('month' => 'March','newUsers' => 25),
                array('month' => 'April','newUsers' => 15),
                array('month' => 'May', 'newUsers' => 5),
                array('month' => 'June', 'newUsers' => 45),
                array('month' => 'July', 'newUsers' => 70),
                array('month' => 'August', 'newUsers' => 17),
                array('month' => 'September', 'newUsers' => 40),
                array('month' => 'October', 'newUsers' => 12),
                array('month' => 'November', 'newUsers' => 47),
                array('month' => 'December', 'newUsers' => 64)
            ),
            'labels' => array('Month', 'New users number'),
            'fields' => array('month', 'newUsers')
        );

        return $this->templating->renderResponse($configuration["template"], array('data' => $data, 'configuration' => $configuration));
    }

    public function getType()
    {
        return 'table';
    }
}