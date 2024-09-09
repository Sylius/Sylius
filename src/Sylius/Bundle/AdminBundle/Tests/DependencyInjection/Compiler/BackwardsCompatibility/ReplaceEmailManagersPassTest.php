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

namespace Sylius\Bundle\AdminBundle\Tests\DependencyInjection\Compiler\BackwardsCompatibility;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction;
use Sylius\Bundle\AdminBundle\Action\ResendShipmentConfirmationEmailAction;
use Sylius\Bundle\AdminBundle\DependencyInjection\Compiler\BackwardsCompatibility\ReplaceEmailManagersPass;
use Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
use Sylius\Bundle\AdminBundle\EventListener\ShipmentShipListener;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendOrderConfirmationEmailDispatcher;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendShipmentConfirmationEmailDispatcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ReplaceEmailManagersPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_replaces_decorated_shipment_email_manager_service_of_shipment_ship_listener_arguments(): void
    {
        $this->setDefinition('sylius.email_manager.shipment', new Definition());
        $this->setDefinition('sylius.email_manager.shipment.decorated', (new Definition())->setDecoratedService('sylius.email_manager.shipment'));
        $this->setDefinition('sylius.mailer.shipment_email_manager.admin', new Definition());
        $this->setDefinition('sylius.listener.shipment_ship', new Definition(ShipmentShipListener::class, [new Reference('sylius.mailer.shipment_email_manager.admin')]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.listener.shipment_ship',
            0,
            'sylius.email_manager.shipment',
        );
    }

    /** @test */
    public function it_does_nothing_if_shipment_email_manager_service_is_not_decorated(): void
    {
        $this->setDefinition('sylius.email_manager.shipment', new Definition());
        $this->setDefinition('sylius.mailer.shipment_email_manager.admin', new Definition());
        $this->setDefinition('sylius.listener.shipment_ship', new Definition(ShipmentShipListener::class, [new Reference('sylius.mailer.shipment_email_manager.admin')]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.listener.shipment_ship',
            0,
            'sylius.mailer.shipment_email_manager.admin',
        );
    }

    /** @test */
    public function it_replaces_decorated_shipment_email_manager_service_of_resend_shipment_confirmation_action_arguments(): void
    {
        $this->setDefinition(ShipmentEmailManagerInterface::class, new Definition());
        $this->setDefinition('sylius.email_manager.shipment.decorated', (new Definition())->setDecoratedService(ShipmentEmailManagerInterface::class));
        $this->setDefinition(ResendShipmentConfirmationEmailDispatcher::class, new Definition());
        $this->setDefinition('sylius.repository.shipment', new Definition());
        $this->setDefinition(ResendShipmentConfirmationEmailAction::class, new Definition(ResendShipmentConfirmationEmailAction::class, [new Reference('sylius.repository.shipment'), new Reference(ResendShipmentConfirmationEmailDispatcher::class)]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            ResendShipmentConfirmationEmailAction::class,
            1,
            ShipmentEmailManagerInterface::class,
        );
    }

    /** @test */
    public function it_does_nothing_if_shipment_email_manager_interface_service_is_not_decorated(): void
    {
        $this->setDefinition(ShipmentEmailManagerInterface::class, new Definition());
        $this->setDefinition(ResendShipmentConfirmationEmailDispatcher::class, new Definition());
        $this->setDefinition('sylius.repository.shipment', new Definition());
        $this->setDefinition(ResendShipmentConfirmationEmailAction::class, new Definition(ResendShipmentConfirmationEmailAction::class, [new Reference('sylius.repository.shipment'), new Reference(ResendShipmentConfirmationEmailDispatcher::class)]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            ResendShipmentConfirmationEmailAction::class,
            1,
            ResendShipmentConfirmationEmailDispatcher::class,
        );
    }

    /** @test */
    public function it_replaces_decorated_order_email_manager_service_of_resend_order_confirmation_action_arguments(): void
    {
        $this->setDefinition(OrderEmailManagerInterface::class, new Definition());
        $this->setDefinition('sylius.email_manager.order.decorated', (new Definition())->setDecoratedService(OrderEmailManagerInterface::class));
        $this->setDefinition(ResendOrderConfirmationEmailDispatcher::class, new Definition());
        $this->setDefinition('sylius.repository.order', new Definition());
        $this->setDefinition(ResendOrderConfirmationEmailAction::class, new Definition(ResendOrderConfirmationEmailAction::class, [new Reference('sylius.repository.order'), new Reference(ResendOrderConfirmationEmailDispatcher::class)]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            ResendOrderConfirmationEmailAction::class,
            1,
            OrderEmailManagerInterface::class,
        );
    }

    /** @test */
    public function it_does_nothing_if_order_email_manager_interface_service_is_not_decorated(): void
    {
        $this->setDefinition(OrderEmailManagerInterface::class, new Definition());
        $this->setDefinition(ResendOrderConfirmationEmailDispatcher::class, new Definition());
        $this->setDefinition('sylius.repository.order', new Definition());
        $this->setDefinition(ResendOrderConfirmationEmailAction::class, new Definition(ResendOrderConfirmationEmailAction::class, [new Reference('sylius.repository.order'), new Reference(ResendOrderConfirmationEmailDispatcher::class)]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            ResendOrderConfirmationEmailAction::class,
            1,
            ResendOrderConfirmationEmailDispatcher::class,
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ReplaceEmailManagersPass());
    }
}
