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
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class RegisterResourcesPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_resource_registry_if_resources_exist()
    {
        $this->setDefinition('sylius.resource_registry', new Definition());

        $this->setParameter(
            'sylius.resources',
            [
                'app.book' => ['classes' => ['model' => \stdClass::class]],
                'app.author' => ['classes' => ['interface' => \Countable::class]],
            ]
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.resource_registry',
            'addFromAliasAndConfiguration',
            ['app.book', ['classes' => ['model' => \stdClass::class]]]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.resource_registry',
            'addFromAliasAndConfiguration',
            ['app.author', ['classes' => ['interface' => \Countable::class]]]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterResourcesPass());
    }
}
