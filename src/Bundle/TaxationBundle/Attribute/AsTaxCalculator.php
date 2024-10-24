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

namespace Sylius\Bundle\TaxationBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsTaxCalculator
{
    public const SERVICE_TAG = 'sylius.tax_calculator';

    public function __construct(
        private string $calculator,
        private int $priority = 0,
    ) {
    }

    public function getCalculator(): string
    {
        return $this->calculator;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
