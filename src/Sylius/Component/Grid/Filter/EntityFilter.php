<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class EntityFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        if (empty($data) || empty($data['id'])) {
            return;
        }

        $field = isset($options['field']) ? $options['field'] : $name;

        $dataSource->restrict($dataSource->getExpressionBuilder()->equals($field, $data['id']));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'entity';
    }
}
