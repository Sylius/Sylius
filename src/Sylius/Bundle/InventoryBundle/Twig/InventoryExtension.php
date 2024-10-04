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

namespace Sylius\Bundle\InventoryBundle\Twig;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class InventoryExtension extends AbstractExtension
{
    public function __construct(private AvailabilityCheckerInterface $availabilityChecker)
    {
    }

    public function getFunctions(): array
    {
        return [
             new TwigFunction('sylius_inventory_is_available', [$this->availabilityChecker, 'isStockAvailable']),
             new TwigFunction('sylius_inventory_is_sufficient', [$this->availabilityChecker, 'isStockSufficient']),
        ];
    }
}
