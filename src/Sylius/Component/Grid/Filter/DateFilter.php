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
final class DateFilter implements FilterInterface
{
    const NAME = 'date';

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $field = isset($options['field']) ? $options['field'] : $name;

        $from = $this->getDateTime($data['from']);
        if (null !== $from) {
            $expressionBuilder->greaterThanOrEqual($field, $from);
        }

        $to = $this->getDateTime($data['to']);
        if (null !== $to) {
            $expressionBuilder->lessThan($field, $to);
        }
    }

    /**
     * @param string[] $data
     *
     * @return null|string
     */
    private function getDateTime(array $data)
    {
        if (empty($data['date'])) {
            return null;
        }

        if (empty($data['time'])) {
            return $data['date'];
        }

        return $data['date'].' '.$data['time'];
    }
}
