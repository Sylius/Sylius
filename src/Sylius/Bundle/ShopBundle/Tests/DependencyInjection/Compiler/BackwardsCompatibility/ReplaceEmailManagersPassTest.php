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

namespace Sylius\Bundle\ShopBundle\Tests\DependencyInjection\Compiler\BackwardsCompatibility;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ShopBundle\DependencyInjection\Compiler\BackwardsCompatibility\ReplaceEmailManagersPass;
use Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ReplaceEmailManagersPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_replaces_decorated_order_email_manager_service_of_order_complete_listener_arguments(): void
    {
        $this->setDefinition('sylius.email_manager.order', new Definition());
        $this->setDefinition('sylius.email_manager.order.decorated', (new Definition())->setDecoratedService('sylius.email_manager.order'));
        $this->setDefinition('sylius.mailer.order_email_manager.shop', new Definition());
        $this->setDefinition('sylius.listener.order_complete', new Definition(OrderCompleteListener::class, [new Reference('sylius.mailer.order_email_manager.shop')]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.listener.order_complete',
            0,
            'sylius.email_manager.order',
        );
    }

    /** @test */
    public function it_does_nothing_if_shipment_email_manager_service_is_not_decorated(): void
    {
        $this->setDefinition('sylius.email_manager.order', new Definition());
        $this->setDefinition('sylius.mailer.order_email_manager.shop', new Definition());
        $this->setDefinition('sylius.listener.order_complete', new Definition(OrderCompleteListener::class, [new Reference('sylius.mailer.order_email_manager.shop')]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.listener.order_complete',
            0,
            'sylius.mailer.order_email_manager.shop',
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ReplaceEmailManagersPass());
    }
}
