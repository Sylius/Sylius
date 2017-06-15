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
    const DEFAULT_INCLUSIVE_FROM = true;
    const DEFAULT_INCLUSIVE_TO = false;

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $field = $this->getOption($options, 'field', $name);

        $from = $this->getDateTime($data['from']);
        if (null !== $from) {
            $inclusive = (bool)$this->getOption($options, 'inclusive_from', self::DEFAULT_INCLUSIVE_FROM);
            if (true === $inclusive) {
                $expressionBuilder->greaterThanOrEqual($field, $from);
            } else {
                $expressionBuilder->greaterThan($field, $from);
            }
        }

        $to = $this->getDateTime($data['to']);
        if (null !== $to) {
            $inclusive = (bool)$this->getOption($options, 'inclusive_to', self::DEFAULT_INCLUSIVE_TO);
            if (true === $inclusive) {
                $expressionBuilder->lessThanOrEqual($field, $to);
            } else {
                $expressionBuilder->lessThan($field, $to);
            }
        }
    }


    /**
     * @param array $options
     * @param string $name
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    private function getOption(array $options, $name, $default = null)
    {
        return isset($options[$name]) ? $options[$name] : $default;
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
