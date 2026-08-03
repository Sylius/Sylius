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

interface ComparisonOperatorMatcherInterface
{
    /**
     * @return array<string, string>
     */
    public function getAvailableComparisonOperators(): array;

    public function getDefaultComparisonOperator(): string;

    public function match(int $subjectValue, int $configuredValue, string $comparisonOperator): bool;
}
