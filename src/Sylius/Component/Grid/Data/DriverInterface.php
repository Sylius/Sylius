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

namespace Sylius\Component\Grid\Data;

use Sylius\Component\Grid\Parameters;

interface DriverInterface
{
    /**
     * @param array $configuration
     * @param Parameters $parameters
     *
     * @return DataSourceInterface
     */
    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface;
}
