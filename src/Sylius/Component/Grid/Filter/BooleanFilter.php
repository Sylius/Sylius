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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class BooleanFilter implements FilterInterface
{
    const TRUE  = 'true';
    const FALSE = 'false';

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        if (empty($data)) {
            return;
        }

        $field = isset($options['field']) ? $options['field'] : $name;

        $data = self::TRUE === $data;

        $dataSource->restrict($dataSource->getExpressionBuilder()->equals($field, $data));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'boolean';
    }
}
