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
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class MoneyFilter implements FilterInterface
{
    const DEFAULT_SCALE = 2;

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        if (empty($data)) {
            return;
        }

        $field = isset($options['field']) ? $options['field'] : $name;
        $scale = isset($options['scale']) ? (int) $options['scale'] : self::DEFAULT_SCALE;

        $greaterThan = $this->getDataValue($data, 'greaterThan');
        $lessThan = $this->getDataValue($data, 'lessThan');

        $expressionBuilder = $dataSource->getExpressionBuilder();

        if (!empty($data['currency'])) {
            $dataSource->restrict($expressionBuilder->equals($options['currency_field'], $data['currency']));
        }
        if ('' !== $greaterThan) {
            $expressionBuilder->greaterThan($field, $this->normalizeAmount($greaterThan, $scale));
        }
        if ('' !== $lessThan) {
            $expressionBuilder->lessThan($field, $this->normalizeAmount($lessThan, $scale));
        }
    }

    /**
     * @param string|float $amount
     * @param int $scale
     *
     * @return int
     */
    private function normalizeAmount($amount, $scale)
    {
        return (int) round($amount * (10 ** $scale));
    }

    /**
     * @param string[] $data
     * @param string $key
     *
     * @return string
     */
    private function getDataValue(array $data, $key)
    {
        return isset($data[$key]) ? $data[$key] : '';
    }
}
