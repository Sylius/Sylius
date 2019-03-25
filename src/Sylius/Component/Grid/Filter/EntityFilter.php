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

final class EntityFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }
        $fields = $options['fields'] ?? [$name];

        $expressionBuilder = $dataSource->getExpressionBuilder();
        $expressionBuilder->

        $expressions = [];
        foreach ($fields as $field) {
            $expressions[] = $expressionBuilder->equals($field, $data);
            //$exp2[] = $expressionBuilder->in('o.channel',$expressions);
            //var_dump($name);
            //var_dump($data);
            //var_dump($exp2);
            //var_dump($field);
        }
        //var_dump($data);
        //var_dump($expressions);

        $dataSource->restrict($expressionBuilder->orX(...$expressions));

        //exit;
    }
}
