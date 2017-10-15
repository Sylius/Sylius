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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface as BaseTaxRateInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxRateInterface extends BaseTaxRateInterface
{
    /**
     * @return ZoneInterface|null
     */
    public function getZone(): ?ZoneInterface;

    /**
     * @param ZoneInterface|null $zone
     */
    public function setZone(?ZoneInterface $zone): void;
}
