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
use Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler\FixtureRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixtureRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_fixtures()
    {
        $this->setDefinition('sylius_fixtures.fixture_registry', new Definition());
        $this->setDefinition('acme.fixture', (new Definition())->addTag('sylius_fixtures.fixture'));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius_fixtures.fixture_registry',
            'addFixture',
            [new Reference('acme.fixture')]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FixtureRegistryPass());
    }
}
