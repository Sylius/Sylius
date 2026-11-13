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

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set('sylius_abstraction.state_machine.adapter.winzou_state_machine', WinzouStateMachineAdapter::class)
        ->args([
            service('sm.factory'),
        ])
        ->tag('sylius.state_machine', ['key' => 'winzou_state_machine']);

    $services->alias(StateMachineInterface::class . ' $winzouStateMachine', 'sylius_abstraction.state_machine.adapter.winzou_state_machine');
};
