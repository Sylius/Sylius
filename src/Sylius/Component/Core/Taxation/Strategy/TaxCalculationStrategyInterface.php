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

namespace Sylius\Component\Core\Taxation\Strategy;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface TaxCalculationStrategyInterface
{
    public function applyTaxes(OrderInterface $order, ZoneInterface $zone): void;

    public function getType(): string;

    public function supports(OrderInterface $order, ZoneInterface $zone): bool;
}
