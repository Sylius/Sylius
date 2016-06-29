<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Grid\Data;

use Sylius\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DriverInterface
{
    /**
     * @param array $configuration
     * @param Parameters $parameters
     *
     * @return DataSourceInterface
     */
    public function getDataSource(array $configuration, Parameters $parameters);
}
