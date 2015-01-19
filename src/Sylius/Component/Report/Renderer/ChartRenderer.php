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
class ChartRenderer implements RendererInterface
{
    public function render($data, $configuration)
    {
    }

    public function getType()
    {
        return 'chart';
    }
}