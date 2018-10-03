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

final class DateFilter implements FilterInterface
{
    public const NAME = 'date';
    public const DEFAULT_INCLUSIVE_FROM = true;
    public const DEFAULT_INCLUSIVE_TO = false;

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $field = (string) $this->getOption($options, 'field', $name);

        $from = isset($data['from']) ? $this->getDateTime($data['from'], '00:00') : null;
        if (null !== $from) {
            $inclusive = (bool) $this->getOption($options, 'inclusive_from', self::DEFAULT_INCLUSIVE_FROM);
            if (true === $inclusive) {
                $dataSource->restrict($expressionBuilder->greaterThanOrEqual($field, $from));
            } else {
                $dataSource->restrict($expressionBuilder->greaterThan($field, $from));
            }
        }

        $to = isset($data['to']) ? $this->getDateTime($data['to'], '23:59') : null;
        if (null !== $to) {
            $inclusive = (bool) $this->getOption($options, 'inclusive_to', self::DEFAULT_INCLUSIVE_TO);
            if (true === $inclusive) {
                $dataSource->restrict($expressionBuilder->lessThanOrEqual($field, $to));
            } else {
                $dataSource->restrict($expressionBuilder->lessThan($field, $to));
            }
        }
    }

    /**
     * @param array $options
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    private function getOption(array $options, string $name, $default)
    {
        return $options[$name] ?? $default;
    }

    /**
     * @param string[] $data
     * @param string $defaultTime
     *
     * @return string|null
     */
    private function getDateTime(array $data, string $defaultTime): ?string
    {
        if (empty($data['date'])) {
            return null;
        }

        if (empty($data['time'])) {
            $data['time'] = $defaultTime;
        }

        return $data['date'] . ' ' . $data['time'];
    }
}
