<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

trigger_deprecation(
    'sylius/core-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    EntitiesFilter::class,
);

/**
 * @experimental
 * @deprecated since Sylius 1.14 and will be removed in Sylius 2.0.
 */
final class EntitiesFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $expressionBuilder = $dataSource->getExpressionBuilder();

        $dataSource->restrict($expressionBuilder->equals($options['field'], $data));
    }
}
