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
            array('month' => 'January','newUsers' => 20),
            array('month' => 'February','newUsers' => 10),
            array('month' => 'March','newUsers' => 25),
            array('month' => 'April','newUsers' =>15)
        );

        $configuration = array('template' => 0);
    }

    public function getType()
    {
        return 'table';
    }
}