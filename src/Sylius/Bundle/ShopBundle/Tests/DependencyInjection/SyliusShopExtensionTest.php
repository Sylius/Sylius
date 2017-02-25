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
use Sylius\Bundle\ShopBundle\Locale\StorageBasedLocaleSwitcher;
use Sylius\Bundle\ShopBundle\Locale\UrlBasedLocaleSwitcher;

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

        $this->assertContainerBuilderHasService('sylius.controller.shop.contact');
        $this->assertContainerBuilderHasService('sylius.controller.shop.currency_switch');
        $this->assertContainerBuilderHasService('sylius.controller.shop.homepage');
        $this->assertContainerBuilderHasService('sylius.controller.shop.locale_switch');
        $this->assertContainerBuilderHasService('sylius.controller.shop.security_widget');
    }

    /**
     * @test
     */
    public function it_loads_all_supported_listeners_by_default()
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.listener.order_customer_ip');
        $this->assertContainerBuilderHasService('sylius.listener.order_complete');
        $this->assertContainerBuilderHasService('sylius.listener.user_registration');
        $this->assertContainerBuilderHasService('sylius.listener.order_promotion_integrity_checker');
        $this->assertContainerBuilderHasService('sylius.listener.order_total_integrity_checker');
        $this->assertContainerBuilderHasService('sylius.order_locale_assigner');
    }

    /**
     * @test
     */
    public function it_loads_menu_services_by_default()
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.shop.menu_builder.account');
    }

    /**
     * @test
     */
    public function it_uses_url_based_locale_strategy_by_default()
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.shop.locale_switcher', UrlBasedLocaleSwitcher::class);
        $this->assertContainerBuilderHasService('sylius.listener.non_channel_request_locale');

        $this->assertContainerBuilderNotHasService('sylius.storage.locale');
        $this->assertContainerBuilderNotHasService('sylius.context.locale.storage_based');
    }

    /**
     * @test
     */
    public function it_uses_storage_based_locale_strategy_when_configured()
    {
        $this->load([
            'locale_switcher' => 'storage',
        ]);

        $this->assertContainerBuilderHasService('sylius.shop.locale_switcher', StorageBasedLocaleSwitcher::class);
        $this->assertContainerBuilderHasService('sylius.storage.locale');
        $this->assertContainerBuilderHasService('sylius.context.locale.storage_based');

        $this->assertContainerBuilderNotHasService('sylius.listener.non_channel_request_locale');
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
