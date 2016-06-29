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

use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;
use Sylius\Registry\ServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DataSourceProvider implements DataSourceProviderInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $driversRegistry;

    /**
     * @param ServiceRegistryInterface $driversRegistry
     */
    public function __construct(ServiceRegistryInterface $driversRegistry)
    {
        $this->driversRegistry = $driversRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(Grid $grid, Parameters $parameters)
    {
        $driver = $grid->getDriver();

        if (!$this->driversRegistry->has($driver)) {
            throw new UnsupportedDriverException($driver);
        }

        return $this->driversRegistry->get($driver)->getDataSource($grid->getDriverConfiguration(), $parameters);
    }
}
