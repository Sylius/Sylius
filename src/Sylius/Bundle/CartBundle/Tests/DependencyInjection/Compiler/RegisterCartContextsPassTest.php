<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
use Sylius\Bundle\CartBundle\DependencyInjection\Compiler\RegisterCartContextsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RegisterCartContextsPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_defined_cart_contexts()
    {
        $this->setDefinition('sylius.registry.cart_context', new Definition());

        $cartContextDefinition = new Definition();
        $cartContextDefinition->addTag('sylius.cart_context');
        $this->setDefinition('sylius.context.cart', $cartContextDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.cart_context',
            'register',
            [
                new Reference('sylius.context.cart'),
                0
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_register_cart_contexts_if_there_is_no_cart_contexts()
    {
        $this->setDefinition('sylius.registry.cart_context', new Definition());

        $this->compile();

        $this->assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(
            'sylius.registry.cart_context',
            'register'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterCartContextsPass());
    }

    /**
     * @param string $serviceId
     * @param string $method
     */
    private function assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall($serviceId, $method)
    {
        $definition = $this->container->findDefinition($serviceId);

        self::assertThat(
            $definition,
            new \PHPUnit_Framework_Constraint_Not(new DefinitionHasMethodCallConstraint($method))
        );
    }
}
