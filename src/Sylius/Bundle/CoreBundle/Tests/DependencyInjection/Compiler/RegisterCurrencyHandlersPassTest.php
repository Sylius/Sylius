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
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterCurrencyHandlersPass;
use Sylius\Bundle\CoreBundle\Handler\CartCurrencyChangeHandler;
use Sylius\Component\Core\Currency\Handler\CompositeCurrencyChangeHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class RegisterCurrencyHandlersPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_composite_currency_change_handler_if_exists()
    {
        $compositeLocaleChangeHandler = new Definition(CompositeCurrencyChangeHandler::class);
        $this->setDefinition('sylius.handler.currency_change', $compositeLocaleChangeHandler);

        $cartLocaleChangeHandler = new Definition(CartCurrencyChangeHandler::class);
        $cartLocaleChangeHandler->addTag('sylius.currency.change_handler');

        $this->setDefinition('sylius.handler.currency_change.cart', $cartLocaleChangeHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.handler.currency_change',
            'addHandler', [
                new Reference('sylius.handler.currency_change.cart'),
                0
            ]
        );
    }

    /**
     * @test
     */
    public function it_adds_method_call_to_composite_currency_change_handler_with_custom_priority()
    {
        $compositeLocaleChangeHandler = new Definition(CompositeCurrencyChangeHandler::class);
        $this->setDefinition('sylius.handler.currency_change', $compositeLocaleChangeHandler);

        $cartLocaleChangeHandler = new Definition(CartCurrencyChangeHandler::class);
        $cartLocaleChangeHandler->addTag('sylius.currency.change_handler', ['priority' => 5]);

        $this->setDefinition('sylius.handler.currency_change.cart', $cartLocaleChangeHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.handler.currency_change',
            'addHandler', [
                new Reference('sylius.handler.currency_change.cart'),
                5
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_method_call_if_there_are_no_tagged_processors()
    {
        $compositeLocaleChangeHandler = new Definition(CompositeCurrencyChangeHandler::class);
        $this->setDefinition('sylius.handler.currency_change', $compositeLocaleChangeHandler);

        $this->assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(
            'sylius.handler.currency_change',
            'addHandler'
        );
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

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterCurrencyHandlersPass());
    }
}
