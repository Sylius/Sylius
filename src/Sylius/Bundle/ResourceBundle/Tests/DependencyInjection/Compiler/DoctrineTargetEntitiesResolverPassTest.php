<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\DoctrineTargetEntitiesResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class DoctrineTargetEntitiesResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_resolve_doctrine_target_entities_with_interface_given_as_fqcn()
    {
        $this->setDefinition('doctrine.orm.listeners.resolve_target_entity', new Definition());

        $this->setParameter(
            'sylius.resources',
            ['app.loremipsum' => ['classes' => ['interface' => \Countable::class]]]
        );

        $this->setParameter('app.model.loremipsum.class', \stdClass::class);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'doctrine.orm.listeners.resolve_target_entity',
            'addResolveTargetEntity',
            [\Countable::class, \stdClass::class, []]
        );
    }

    /**
     * @test
     */
    public function it_adds_method_call_to_resolve_doctrine_target_entities_with_interface_given_as_parameter()
    {
        $this->setDefinition('doctrine.orm.listeners.resolve_target_entity', new Definition());

        $this->setParameter(
            'sylius.resources',
            ['app.loremipsum' => ['classes' => ['interface' => 'app.interface.loremipsum.class']]]
        );

        $this->setParameter('app.model.loremipsum.class', \stdClass::class);
        $this->setParameter('app.interface.loremipsum.class', \Countable::class);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'doctrine.orm.listeners.resolve_target_entity',
            'addResolveTargetEntity',
            [\Countable::class, \stdClass::class, []]
        );
    }

    /**
     * @test
     */
    public function it_ignores_resources_without_interface()
    {
        $this->setDefinition('doctrine.orm.listeners.resolve_target_entity', new Definition());

        $this->setParameter(
            'sylius.resources',
            ['app.loremipsum' => ['classes' => ['model' => \stdClass::class]]]
        );

        $this->compile();
    }

    /**
     * @test
     */
    public function it_adds_doctrine_event_listener_tag_to_target_entities_resolver_if_not_exists()
    {
        $this->setDefinition('doctrine.orm.listeners.resolve_target_entity', new Definition());
        $this->setParameter('sylius.resources', []);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'doctrine.orm.listeners.resolve_target_entity',
            'doctrine.event_listener',
            ['event' => 'loadClassMetadata']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DoctrineTargetEntitiesResolverPass());
    }
}
