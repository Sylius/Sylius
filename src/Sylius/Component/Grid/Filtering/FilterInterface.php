<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Filtering;

use Sylius\Component\Grid\Data\DataSourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface FilterInterface
{
    /**
     * @param DataSourceInterface $dataSource
     * @param string $name
     * @param mixed $data
     * @param array $options
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options);
}
