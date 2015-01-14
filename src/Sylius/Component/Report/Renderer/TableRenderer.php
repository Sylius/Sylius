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

use Symfony\Component\Templating\EngineInterface;
use Sylius\Component\Report\Renderer\RendererInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TableRenderer implements RendererInterface
{
    private $engineInterface;

    public function __construct()
    {
        // $this->engineInterface = new EngineInterface();
    }

    public function render($data, $configuration)
    {
        // $this->engineInterface->render('table-layout.twig', $data);
    }

    public function getType()
    {
        return 'table';
    }
}