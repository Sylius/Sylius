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

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\Collection;

interface RecursiveAdjustmentsAwareInterface
{
    /**
     * @return Collection<array-key, AdjustmentInterface>
     */
    public function getAdjustmentsRecursively(?string $type = null): Collection;

    public function removeAdjustmentsRecursively(?string $type = null): void;

    public function getAdjustmentsTotalRecursively(?string $type = null): int;
}
