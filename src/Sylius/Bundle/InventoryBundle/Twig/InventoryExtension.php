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

use Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class InventoryExtension extends AbstractExtension
{
    public function __construct(private InventoryHelper|AvailabilityCheckerInterface $helper)
    {
        if ($this->helper instanceof InventoryHelper) {
            trigger_deprecation(
                'sylius/inventory-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                InventoryHelper::class,
                self::class,
                AvailabilityCheckerInterface::class,
            );

            trigger_deprecation(
                'sylius/inventory-bundle',
                '1.14',
                'The argument name $helper is deprecated and will be renamed to $availabilityChecker in Sylius 2.0.',
            );
        }
    }

    public function getFunctions(): array
    {
        return [
             new TwigFunction('sylius_inventory_is_available', [$this->helper, 'isStockAvailable']),
             new TwigFunction('sylius_inventory_is_sufficient', [$this->helper, 'isStockSufficient']),
        ];
    }
}
