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
        $this->setDefinition('acme.fixture', (new Definition())->addTag('sylius_fixtures.fixture', ['fixture-name' => 'foobar']));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius_fixtures.fixture_registry',
            'addFixture',
            ['foobar', new Reference('acme.fixture')]
        );
    }

    /**
     * @test
     */
    public function it_registers_fixtures_under_multiple_names()
    {
        $fixtureDefinition = new Definition();
        $fixtureDefinition->addTag('sylius_fixtures.fixture', ['fixture-name' => 'foo']);
        $fixtureDefinition->addTag('sylius_fixtures.fixture', ['fixture-name' => 'bar']);

        $this->setDefinition('sylius_fixtures.fixture_registry', new Definition());
        $this->setDefinition('acme.fixture', $fixtureDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius_fixtures.fixture_registry',
            'addFixture',
            ['foo', new Reference('acme.fixture')]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius_fixtures.fixture_registry',
            'addFixture',
            ['bar', new Reference('acme.fixture')]
        );
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_if_tag_does_not_include_name_attribute()
    {
        $this->setDefinition('sylius_fixtures.fixture_registry', new Definition());
        $this->setDefinition('acme.fixture', (new Definition())->addTag('sylius_fixtures.fixture'));

        $this->compile();
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FixtureRegistryPass());
    }
}
