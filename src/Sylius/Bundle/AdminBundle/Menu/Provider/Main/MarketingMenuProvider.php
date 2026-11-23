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

final readonly class MarketingMenuProvider implements MenuProviderInterface
{
    public function __invoke(ItemInterface $menu): void
    {
        $marketing = $menu
            ->addChild('marketing')
            ->setLabel('sylius.menu.admin.main.marketing.header')
            ->setLabelAttribute('icon', 'tabler:percentage')
        ;

        $marketing
            ->addChild('promotions', ['route' => 'sylius_admin_promotion_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_promotion_create'],
                ['route' => 'sylius_admin_promotion_update'],
                ['route' => 'sylius_admin_promotion_coupon_index'],
                ['route' => 'sylius_admin_promotion_coupon_create'],
                ['route' => 'sylius_admin_promotion_coupon_update'],
                ['route' => 'sylius_admin_promotion_coupon_generate'],
            ]]])
            ->setLabel('sylius.menu.admin.main.marketing.cart_promotions')
            ->setLabelAttribute('icon', 'tabler:shopping-cart-down')
        ;

        $marketing
            ->addChild('catalog_promotions', ['route' => 'sylius_admin_catalog_promotion_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_catalog_promotion_create'],
                ['route' => 'sylius_admin_catalog_promotion_update'],
                ['route' => 'sylius_admin_catalog_promotion_show'],
            ]]])
            ->setLabel('sylius.menu.admin.main.marketing.catalog_promotions')
            ->setLabelAttribute('icon', 'tabler:bookmark')
        ;

        $marketing
            ->addChild('product_reviews', ['route' => 'sylius_admin_product_review_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_review_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.marketing.product_reviews')
            ->setLabelAttribute('icon', 'tabler:news')
        ;
    }
}
