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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\InventoryBundle\Twig\InventoryExtension;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStockValidator;
use Sylius\Component\Inventory\Checker\AvailabilityChecker;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.checker.inventory.availability', AvailabilityChecker::class);

    $services->alias(AvailabilityCheckerInterface::class, 'sylius.checker.inventory.availability');

    $services->set('sylius.validator.in_stock', InStockValidator::class)
        ->args([service('sylius.checker.inventory.availability')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_in_stock']);

    $services->set('sylius.twig.extension.inventory', InventoryExtension::class)
        ->args([service('sylius.checker.inventory.availability')])
        ->tag('twig.extension');
};
