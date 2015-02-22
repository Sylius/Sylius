<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Renderer;

use Sylius\Component\Grid\Definition\Column;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ColumnRendererInterface
{
    /**
     * Renders the column value using appropriate column type.
     *
     * @param array|object $data   The data to be used
     * @param Column       $column The column definition
     */
    public function render($data, Column $column);
}
