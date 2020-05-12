<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class EntitiesFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $expressionBuilder = $dataSource->getExpressionBuilder();

        $dataSource->restrict($expressionBuilder->equals($options['field'], $data));
    }
}
