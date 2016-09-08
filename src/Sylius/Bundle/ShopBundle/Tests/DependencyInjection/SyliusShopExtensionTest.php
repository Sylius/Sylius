<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ShopBundle\DependencyInjection\SyliusShopExtension;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SyliusShopExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_loads_all_supported_controllers_by_default()
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.controller.shop.homepage');
        $this->assertContainerBuilderHasService('sylius.controller.shop.currency');
        $this->assertContainerBuilderHasService('sylius.controller.shop.locale');
    }

    /**
     * @test
     */
    public function it_loads_all_supported_listeners_by_default()
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.listener.checkout_complete');
    }

    /**
     * @test
     */
    public function it_loads_menu_services_by_default()
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.menu_builder.shop.account');
        $this->assertContainerBuilderHasService('sylius.menu.shop.account');
    }

    /**
     * @test
     */
    public function it_loads_checkout_resolver_services_by_default()
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.resolver.checkout');
        $this->assertContainerBuilderHasService('sylius.router.checkout_state');
    }

    /**
     * @test
     */
    public function it_does_not_load_checkout_resolver_services_if_it_is_disabled()
    {
        $this->load([
            'checkout_resolver' => [
                'enabled' => false,
            ],
        ]);

        $this->assertContainerBuilderNotHasService('sylius.resolver.checkout');
        $this->assertContainerBuilderNotHasService('sylius.router.checkout_state');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new SyliusShopExtension(),
        ];
    }
}
