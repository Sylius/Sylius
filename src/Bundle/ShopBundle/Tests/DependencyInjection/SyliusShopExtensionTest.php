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

namespace Sylius\Bundle\ShopBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ShopBundle\DependencyInjection\SyliusShopExtension;
use Sylius\Bundle\ShopBundle\Locale\StorageBasedLocaleSwitcher;
use Sylius\Bundle\ShopBundle\Locale\UrlBasedLocaleSwitcher;
use Sylius\Bundle\ThemeBundle\DependencyInjection\SyliusThemeExtension;

final class SyliusShopExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_loads_all_supported_controllers_by_default(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius_shop.controller.contact');
        $this->assertContainerBuilderHasService('sylius_shop.controller.currency_switch');
        $this->assertContainerBuilderHasService('sylius_shop.controller.locale_switch');
    }

    /**
     * @test
     */
    public function it_loads_all_supported_listeners_by_default(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius_shop.listener.order_customer_ip');
        $this->assertContainerBuilderHasService('sylius_shop.listener.order_complete');
        $this->assertContainerBuilderHasService('sylius_shop.listener.user_registration');
        $this->assertContainerBuilderHasService('sylius_shop.listener.order_integrity_checker');
        $this->assertContainerBuilderHasService('sylius_shop.listener.order_locale_assigner');
    }

    /**
     * @test
     */
    public function it_loads_menu_services_by_default(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius_shop.menu_builder.account');
    }

    /**
     * @test
     */
    public function it_uses_url_based_locale_strategy_by_default(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius_shop.locale_switcher', UrlBasedLocaleSwitcher::class);
        $this->assertContainerBuilderHasService('sylius_shop.listener.non_channel_locale');

        $this->assertContainerBuilderNotHasService('sylius_shop.storage.locale');
        $this->assertContainerBuilderNotHasService('sylius_shop.context.locale.storage_based');
    }

    /**
     * @test
     */
    public function it_uses_storage_based_locale_strategy_when_configured(): void
    {
        $this->load([
            'locale_switcher' => 'storage',
        ]);

        $this->assertContainerBuilderHasService('sylius_shop.locale_switcher', StorageBasedLocaleSwitcher::class);
        $this->assertContainerBuilderHasService('sylius_shop.storage.locale');
        $this->assertContainerBuilderHasService('sylius_shop.context.locale.storage_based');

        $this->assertContainerBuilderNotHasService('sylius_shop.listener.non_channel_locale');
    }

    /**
     * @test
     */
    public function it_loads_checkout_resolver_services_by_default(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.resolver.checkout');
        $this->assertContainerBuilderHasService('sylius.router.checkout_state');
    }

    /**
     * @test
     */
    public function it_does_not_load_checkout_resolver_services_if_it_is_disabled(): void
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
     * @test
     */
    public function it_configures_default_firewall_context_parameter_for_user_registration_listener(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasParameter('sylius_shop.firewall_context_name', 'shop');
    }

    /**
     * @test
     */
    public function it_configures_firewall_context_parameter_for_user_registration_listener_depending_on_custom_configuration(): void
    {
        $this->load(['firewall_context_name' => 'myshopfirewall']);

        $this->assertContainerBuilderHasParameter('sylius_shop.firewall_context_name', 'myshopfirewall');
    }

    /** @test */
    public function it_prepends_sylius_theme_configuration_with_channel_based_context(): void
    {
        $this->container->registerExtension(new SyliusThemeExtension());

        $this->load();

        $syliusThemeConfig = $this->container->getExtensionConfig('sylius_theme')[0];

        $this->assertSame('sylius_shop.theme.context.channel_based', $syliusThemeConfig['context']);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusShopExtension(),
        ];
    }
}
