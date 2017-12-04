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

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface AdjustmentFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     * @param string $label
     * @param int $amount
     * @param bool $neutral
     *
     * @return AdjustmentInterface
     */
    public function createWithData(string $type, string $label, int $amount, bool $neutral = false): AdjustmentInterface;
}
