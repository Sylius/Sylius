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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection\Compiler\BackwardsCompatibility;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility\CancelOrderStateMachineCallbackPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CancelOrderStateMachineCallbackPassTest extends AbstractCompilerPassTestCase
{
    public array $smConfigs = [
        'sylius_order' => [
            'class' => 'Sylius\\Component\\Core\\Model\\Order',
            'property_path' => 'state',
            'graph' => 'sylius_order',
            'state_machine_class' => 'Sylius\\Component\\Resource\\StateMachine\\StateMachine',
            'states' => [
                'cart',
                'new',
                'cancelled',
                'fulfilled',
            ],
            'transitions' => [
                'create' => [
                    'from' => [
                        'cart',
                    ],
                    'to' => 'new',
                ],
                'cancel' => [
                    'from' => [
                        'new',
                    ],
                    'to' => 'cancelled',
                ],
                'fulfill' => [
                    'from' => [
                        'new',
                    ],
                    'to' => 'fulfilled',
                ],
            ],
            'callbacks' => [
                'before' => [],
                'after' => [
                    'sylis_cancel_order' => [
                        'on' => [
                            'cancel',
                        ],
                        'do' => [
                            '@sylius.inventory.order_inventory_operator',
                            'cancel',
                        ],
                        'args' => [
                            'object',
                        ],
                        'disabled' => false,
                        'priority' => 0,
                    ],
                ],
                'guard' => [],
            ],
        ],
    ];

    /** @test */
    public function it_converts_from_old_name_to_new_name(): void
    {
        $this->setParameter('sm.configs', $this->smConfigs);
        $this->compile();

        $smConfigs = $this->container->getParameter('sm.configs');
        $this->assertFalse(
            isset($smConfigs['sylius_order']['callbacks']['after']['sylis_cancel_order']),
            'State machine "sylius_order" should not have "sylis_cancel_order" callback configured.',
        );
        $this->assertTrue(
            isset($smConfigs['sylius_order']['callbacks']['after']['sylius_cancel_order']),
            'State machine "sylius_order" should have "sylius_cancel_order" callback configured.',
        );
        $this->assertEquals(
            $this->smConfigs['sylius_order']['callbacks']['after']['sylis_cancel_order'],
            $smConfigs['sylius_order']['callbacks']['after']['sylius_cancel_order'],
            'State machine "sylius_order" should have the "sylis_cancel_order" callback moved to "sylius_cancel_order".',
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CancelOrderStateMachineCallbackPass());
    }
}
