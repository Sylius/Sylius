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

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @implements AdjustmentFactoryInterface<AdjustmentInterface>
 */
class AdjustmentFactory implements AdjustmentFactoryInterface
{
    public function __construct(private FactoryInterface $adjustmentFactory)
    {
    }

    public function createNew(): AdjustmentInterface
    {
        return $this->adjustmentFactory->createNew();
    }

    public function createWithData(
        string $type,
        string $label,
        int $amount,
        bool $neutral = false,
        array $details = [],
    ): AdjustmentInterface {
        $adjustment = $this->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($label);
        $adjustment->setAmount($amount);
        $adjustment->setNeutral($neutral);
        $adjustment->setDetails($details);

        return $adjustment;
    }
}
