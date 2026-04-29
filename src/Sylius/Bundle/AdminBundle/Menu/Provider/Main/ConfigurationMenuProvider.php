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

namespace Sylius\Bundle\AdminBundle\Menu\Provider\Main;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Menu\Provider\MenuProviderInterface;

final readonly class ConfigurationMenuProvider implements MenuProviderInterface
{
    public function __invoke(ItemInterface $menu): void
    {
        $configuration = $menu
            ->addChild('configuration')
            ->setLabel('sylius.menu.admin.main.configuration.header')
            ->setLabelAttribute('icon', 'tabler:adjustments')
        ;

        $configuration
            ->addChild('channels', ['route' => 'sylius_admin_channel_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_channel_create'],
                ['route' => 'sylius_admin_channel_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.channels')
            ->setLabelAttribute('icon', 'tabler:arrows-shuffle')
        ;

        $configuration
            ->addChild('countries', ['route' => 'sylius_admin_country_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_country_create'],
                ['route' => 'sylius_admin_country_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.countries')
            ->setLabelAttribute('icon', 'tabler:flag')
        ;

        $configuration
            ->addChild('zones', ['route' => 'sylius_admin_zone_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_zone_create'],
                ['route' => 'sylius_admin_zone_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.zones')
            ->setLabelAttribute('icon', 'tabler:world')
        ;

        $configuration
            ->addChild('currencies', ['route' => 'sylius_admin_currency_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_currency_create'],
                ['route' => 'sylius_admin_currency_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.currencies')
            ->setLabelAttribute('icon', 'tabler:currency-dollar')
        ;

        $configuration
            ->addChild('exchange_rates', ['route' => 'sylius_admin_exchange_rate_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_exchange_rate_create'],
                ['route' => 'sylius_admin_exchange_rate_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.exchange_rates')
            ->setLabelAttribute('icon', 'tabler:adjustments')
        ;

        $configuration
            ->addChild('locales', ['route' => 'sylius_admin_locale_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_locale_create'],
                ['route' => 'sylius_admin_locale_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.locales')
            ->setLabelAttribute('icon', 'tabler:bubble-text')
        ;

        $configuration
            ->addChild('payment_methods', ['route' => 'sylius_admin_payment_method_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_payment_method_create'],
                ['route' => 'sylius_admin_payment_method_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.payment_methods')
            ->setLabelAttribute('icon', 'tabler:credit-card-pay')
        ;

        $configuration
            ->addChild('shipping_methods', ['route' => 'sylius_admin_shipping_method_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_shipping_method_create'],
                ['route' => 'sylius_admin_shipping_method_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.shipping_methods')
            ->setLabelAttribute('icon', 'tabler:truck')
        ;

        $configuration
            ->addChild('shipping_categories', ['route' => 'sylius_admin_shipping_category_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_shipping_category_create'],
                ['route' => 'sylius_admin_shipping_category_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.shipping_categories')
            ->setLabelAttribute('icon', 'tabler:layout-list')
        ;

        $configuration
            ->addChild('tax_categories', ['route' => 'sylius_admin_tax_category_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_tax_category_create'],
                ['route' => 'sylius_admin_tax_category_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.tax_categories')
            ->setLabelAttribute('icon', 'tabler:tags')
        ;

        $configuration
            ->addChild('tax_rates', ['route' => 'sylius_admin_tax_rate_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_tax_rate_create'],
                ['route' => 'sylius_admin_tax_rate_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.tax_rates')
            ->setLabelAttribute('icon', 'tabler:coins')
        ;

        $configuration
            ->addChild('admin_users', ['route' => 'sylius_admin_admin_user_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_admin_user_create'],
                ['route' => 'sylius_admin_admin_user_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.admin_users')
            ->setLabelAttribute('icon', 'tabler:lock')
        ;
    }
}
