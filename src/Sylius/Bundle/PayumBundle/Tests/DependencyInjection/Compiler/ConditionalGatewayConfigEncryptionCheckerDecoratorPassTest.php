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

namespace Sylius\Bundle\PayumBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\PayumBundle\DependencyInjection\Compiler\ConditionalGatewayConfigEncryptionCheckerDecoratorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ConditionalGatewayConfigEncryptionCheckerDecoratorPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_registers_gateway_config_encryption_checker_decorator_if_checker_service_exists(): void
    {
        $this->setDefinition('sylius.checker.gateway_config_encryption', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius_payum.checker.gateway_config_encryption', 0, new Reference('.inner'));
    }

    /** @test */
    public function it_does_not_register_gateway_config_encryption_checker_decorator_if_checker_service_does_not_exist(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius_payum.checker.gateway_config_encryption');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConditionalGatewayConfigEncryptionCheckerDecoratorPass());
    }
}
