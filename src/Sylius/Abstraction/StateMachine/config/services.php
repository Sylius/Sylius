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

use Sylius\Abstraction\StateMachine\CompositeStateMachine;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\SymfonyWorkflowAdapter;
use Sylius\Abstraction\StateMachine\Twig\StateMachineExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set('sylius_abstraction.state_machine', CompositeStateMachine::class)
        ->args([
            tagged_iterator('sylius.state_machine', indexAttribute: 'key'),
            '%sylius_abstraction.state_machine.default_adapter%',
            '%sylius_abstraction.state_machine.graphs_to_adapters_mapping%',
        ]);

    $services->alias(StateMachineInterface::class . ' $compositeStateMachine', 'sylius_abstraction.state_machine.composite');

    $services->alias('sylius_abstraction.state_machine.composite', 'sylius_abstraction.state_machine');
    $services->alias(StateMachineInterface::class, 'sylius_abstraction.state_machine');

    $services->set('sylius_abstraction.state_machine.adapter.symfony_workflow', SymfonyWorkflowAdapter::class)
        ->args([
            service('workflow.registry'),
        ])
        ->tag('sylius.state_machine', ['key' => 'symfony_workflow']);

    $services->alias(StateMachineInterface::class . ' $symfonyWorkflow', 'sylius_abstraction.state_machine.adapter.symfony_workflow');

    $services->set('sylius_abstraction.twig.extension.state_machine', StateMachineExtension::class)
        ->args([
            service('sylius_abstraction.state_machine'),
        ])
        ->tag('twig.extension');
};
