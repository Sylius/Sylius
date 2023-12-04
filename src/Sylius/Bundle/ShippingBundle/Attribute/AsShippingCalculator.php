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

namespace Sylius\Bundle\ShippingBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsShippingCalculator
{
    public const SERVICE_TAG = 'sylius.shipping_calculator';

    public function __construct(
        private string $calculator,
        private string $label,
        private string $formType,
        private int $priority = 0,
    ) {
    }

    public function getCalculator(): string
    {
        return $this->calculator;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
