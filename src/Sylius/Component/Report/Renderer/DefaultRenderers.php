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

/**
 * Default renderers.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DefaultRenderers
{
    /**
     * Table renderer
     */
    const TABLE = 'sylius_renderer_table';

    /**
     * Chart renderer
     */
    const CHART = 'sylius_renderer_chart';
}
