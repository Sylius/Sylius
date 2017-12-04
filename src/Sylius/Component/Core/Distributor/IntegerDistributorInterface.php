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

namespace Sylius\Component\Core\Distributor;

interface IntegerDistributorInterface
{
    /**
     * @param float $amount
     * @param int $numberOfTargets
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function distribute(float $amount, int $numberOfTargets): array;
}
