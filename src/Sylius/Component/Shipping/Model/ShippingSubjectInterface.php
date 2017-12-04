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

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;

interface ShippingSubjectInterface
{
    /**
     * @return float
     */
    public function getShippingWeight(): float;

    /**
     * @return float
     */
    public function getShippingVolume(): float;

    /**
     * @return int
     */
    public function getShippingUnitCount(): int;

    /**
     * @return int
     */
    public function getShippingUnitTotal(): int;

    /**
     * @return Collection|ShippableInterface[]
     */
    public function getShippables(): Collection;
}
