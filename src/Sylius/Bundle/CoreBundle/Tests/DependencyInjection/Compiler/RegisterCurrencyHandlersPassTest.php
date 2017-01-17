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
use Sylius\Component\Core\Currency\Handler\CompositeCurrencyChangeHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class RegisterCurrencyHandlersPassTest extends AbstractCompilerPassTestCase
{
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
