<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler\ListenerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ListenerRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_listeners()
    {
        $this->setDefinition('sylius_fixtures.listener_registry', new Definition());
        $this->setDefinition('acme.listener', (new Definition())->addTag('sylius_fixtures.listener'));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius_fixtures.listener_registry',
            'addListener',
            [new Reference('acme.listener')]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ListenerRegistryPass());
    }
}
