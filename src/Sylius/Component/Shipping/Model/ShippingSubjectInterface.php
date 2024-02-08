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

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;

interface ShippingSubjectInterface
{
    public function getShippingWeight(): float;

    public function getShippingVolume(): float;

    public function getShippingUnitCount(): int;

    public function getShippingUnitTotal(): int;

    /**
     * @return Collection<array-key, ShippableInterface>
     */
    public function getShippables(): Collection;
}
