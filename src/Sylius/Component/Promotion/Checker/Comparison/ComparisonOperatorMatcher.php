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

namespace Sylius\Component\Promotion\Checker\Comparison;

final class ComparisonOperatorMatcher implements ComparisonOperatorMatcherInterface
{
    public function getAvailableComparisonOperators(): array
    {
        return [
            'greater_than_equal' => '>=',
            'equal' => '===',
            'different' => '!==',
            'lower_than' => '<',
            'lower_than_equal' => '<=',
            'greater_than' => '>',
        ];
    }

    public function getDefaultComparisonOperator(): string
    {
        return '>=';
    }

    public function match(int $subjectValue, int $configuredValue, string $comparisonOperator): bool
    {
        return match ($comparisonOperator) {
            '>=' => $subjectValue >= $configuredValue,
            '===' => $subjectValue === $configuredValue,
            '!==' => $subjectValue !== $configuredValue,
            '<' => $subjectValue < $configuredValue,
            '<=' => $subjectValue <= $configuredValue,
            '>' => $subjectValue > $configuredValue,
            default => false,
        };
    }
}
