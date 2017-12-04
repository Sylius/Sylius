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

final class MoneyFilter implements FilterInterface
{
    public const DEFAULT_SCALE = 2;

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $field = $options['field'] ?? $name;
        $scale = isset($options['scale']) ? (int) $options['scale'] : self::DEFAULT_SCALE;

        $greaterThan = $this->getDataValue($data, 'greaterThan');
        $lessThan = $this->getDataValue($data, 'lessThan');

        $expressionBuilder = $dataSource->getExpressionBuilder();

        if (!empty($data['currency'])) {
            $dataSource->restrict($expressionBuilder->equals($options['currency_field'], $data['currency']));
        }
        if ('' !== $greaterThan) {
            $expressionBuilder->greaterThan($field, $this->normalizeAmount((float) $greaterThan, $scale));
        }
        if ('' !== $lessThan) {
            $expressionBuilder->lessThan($field, $this->normalizeAmount((float) $lessThan, $scale));
        }
    }

    /**
     * @param float $amount
     * @param int $scale
     *
     * @return int
     */
    private function normalizeAmount(float $amount, int $scale): int
    {
        return (int) round($amount * (10 ** $scale));
    }

    /**
     * @param string[] $data
     * @param string $key
     *
     * @return string
     */
    private function getDataValue(array $data, string $key): string
    {
        return $data[$key] ?? '';
    }
}
