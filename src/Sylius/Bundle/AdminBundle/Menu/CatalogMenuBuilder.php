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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\ItemInterface;

final readonly class CatalogMenuBuilder implements MenuBuilderInterface
{
    public function __construct(
        private MenuBuilderInterface $menuBuilder,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->menuBuilder->createMenu($options);

        $catalog = $menu
            ->addChild('catalog')
            ->setLabel('sylius.menu.admin.main.catalog.header')
            ->setLabelAttribute('icon', 'tabler:list-details')
            ->setExtra('always_open', true)
        ;

        $catalog
            ->addChild('taxons', ['route' => 'sylius_admin_taxon_create', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_taxon_create_for_parent'],
                ['route' => 'sylius_admin_taxon_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.taxons')
            ->setLabelAttribute('icon', 'tabler:folder')
        ;

        $catalog
            ->addChild('products', ['route' => 'sylius_admin_product_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_create'],
                ['route' => 'sylius_admin_product_create_simple'],
                ['route' => 'sylius_admin_product_update'],
                ['route' => 'sylius_admin_product_show'],
                ['route' => 'sylius_admin_product_variant_index'],
                ['route' => 'sylius_admin_product_variant_create'],
                ['route' => 'sylius_admin_product_variant_update'],
                ['route' => 'sylius_admin_product_variant_generate'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.products')
            ->setLabelAttribute('icon', 'tabler:cube')
        ;

        $catalog
            ->addChild('inventory', ['route' => 'sylius_admin_inventory_index'])
            ->setLabel('sylius.menu.admin.main.catalog.inventory')
            ->setLabelAttribute('icon', 'tabler:history')
        ;

        $catalog
            ->addChild('attributes', ['route' => 'sylius_admin_product_attribute_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_attribute_create'],
                ['route' => 'sylius_admin_product_attribute_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.attributes')
            ->setLabelAttribute('icon', 'tabler:cube-spark')
        ;

        $catalog
            ->addChild('options', ['route' => 'sylius_admin_product_option_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_option_create'],
                ['route' => 'sylius_admin_product_option_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.options')
            ->setLabelAttribute('icon', 'tabler:settings')
        ;

        $catalog
            ->addChild('association_types', ['route' => 'sylius_admin_product_association_type_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_association_type_create'],
                ['route' => 'sylius_admin_product_association_type_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.association_types')
            ->setLabelAttribute('icon', 'tabler:subtask')
        ;

        return $menu;
    }
}
