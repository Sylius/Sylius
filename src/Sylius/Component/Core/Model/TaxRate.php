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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Taxation\Model\TaxRate as BaseTaxRate;

class TaxRate extends BaseTaxRate implements TaxRateInterface
{
    /** @var ZoneInterface|null */
    protected $zone;

    public function getZone(): ?ZoneInterface
    {
        return $this->zone;
    }

    public function setZone(?ZoneInterface $zone): void
    {
        $this->zone = $zone;
    }
}
