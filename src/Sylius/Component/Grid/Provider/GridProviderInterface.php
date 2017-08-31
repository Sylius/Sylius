<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Provider;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface GridProviderInterface
{
    /**
     * @param string $code
     *
     * @return Grid
     *
     * @throws UndefinedGridException
     */
    public function get(string $code): Grid;
}
