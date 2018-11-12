<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
use PHPUnit\Framework\Constraint\LogicalNot;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourceRepositoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterResourceRepositoryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_resource_repository_to_resource_repository_registry(): void
    {
        $this->setDefinition('sylius.registry.resource_repository', new Definition());
        $this->setDefinition('sylius.repository.product', new Definition());

        $this->setParameter(
            'sylius.resources',
            ['sylius.product' => []]
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.resource_repository',
            'register',
            ['sylius.product', new Reference('sylius.repository.product')]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_resource_repository_to_resource_repository_registry_if_registry_does_not_exist(): void
    {
        $this->setDefinition('sylius.repository.product', new Definition());

        $this->setParameter(
            'sylius.resources',
            ['sylius.product' => []]
        );

        $this->compile();
    }

    /**
     * @test
     */
    public function it_does_not_add_resource_repository_to_resource_repository_registry_if_resources_do_not_exist(): void
    {
        $this->setDefinition('sylius.registry.resource_repository', new Definition());
        $this->setDefinition('sylius.repository.product', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasServiceDefinitionWithMethodCall(
            'sylius.registry.resource_repository',
            'register',
            ['sylius.product', new Reference('sylius.repository.product')]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_resource_repository_to_resource_repository_registry_if_resource_repository_does_not_exist(): void
    {
        $this->setDefinition('sylius.registry.resource_repository', new Definition());

        $this->setParameter(
            'sylius.resources',
            ['sylius.product' => []]
        );

        $this->compile();

        $this->assertContainerBuilderNotHasServiceDefinitionWithMethodCall(
            'sylius.registry.resource_repository',
            'register',
            ['sylius.product', new Reference('sylius.repository.product')]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterResourceRepositoryPass());
    }

    private function assertContainerBuilderNotHasServiceDefinitionWithMethodCall(
        string $serviceId,
        string $method,
        array $arguments
    ): void {
        $definition = $this->container->findDefinition($serviceId);

        self::assertThat(
            $definition,
            new LogicalNot(new DefinitionHasMethodCallConstraint($method, $arguments))
        );
    }
}
