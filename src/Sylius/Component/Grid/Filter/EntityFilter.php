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
        if (empty($data)) {
            return;
        }

        $fields = isset($options['fields']) ? $options['fields'] : [$name];

        $expressionBuilder = $dataSource->getExpressionBuilder();

        $expressions = [];
        foreach ($fields as $field) {
            $expressions[] = $expressionBuilder->equals($field, $data);
        }

        $dataSource->restrict($expressionBuilder->orX(...$expressions));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'entity';
    }
}
