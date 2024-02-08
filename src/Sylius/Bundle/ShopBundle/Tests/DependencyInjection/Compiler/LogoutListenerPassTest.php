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

namespace Sylius\Bundle\ShopBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ShopBundle\DependencyInjection\Compiler\LogoutListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListenerPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_does_nothing_when_no_shop_firewall_context_name_parameter_is_present(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.handler.shop_user_logout');
    }

    /** @test */
    public function it_does_nothing_when_no_shop_firewall_event_dispatcher_is_present(): void
    {
        $this->container->setParameter('sylius_shop.firewall_context_name', 'shop_firewall');

        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.handler.shop_user_logout');
    }

    /** @test */
    public function it_adds_logout_listener_when_shop_firewall_event_dispatcher_is_present(): void
    {
        $this->container->setParameter('sylius_shop.firewall_context_name', 'shop_firewall');
        $this->container->setDefinition('security.event_dispatcher.shop_firewall', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.handler.shop_user_logout',
            'kernel.event_listener',
            [
                'event' => LogoutEvent::class,
                'dispatcher' => 'security.event_dispatcher.shop_firewall',
                'method' => 'onLogout',
            ],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LogoutListenerPass());
    }
}
