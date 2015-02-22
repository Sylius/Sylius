<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Provider;

use Sylius\Component\Grid\Definition\Grid;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
class ArrayGridProvider implements GridProviderInterface
{
    /**
     * @var array
     */
    private $grids = array();

    /**
     * @param array $grids
     */
    public function __construct($grids)
    {
        $this->grids = $grids;
    }

    /**
     * @param string $name
     */
    public function getGrid($name)
    {
        if (!isset($this->grids[$name])) {
            throw new UndefinedGridException($name);
        }

        return Grid::createFromArray($this->grids[$name]);
    }
}
