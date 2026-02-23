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

use Sylius\Behat\Page\Admin\PromotionCoupon\GeneratePage;
use Sylius\Behat\Page\Admin\PromotionCoupon\IndexPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.promotion_coupon.create.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.promotion_coupon.generate.class', GeneratePage::class);
    $parameters->set('sylius.behat.page.admin.promotion_coupon.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.promotion_coupon.update.class', '%sylius.behat.page.admin.crud.update.class%');

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.promotion_coupon.create', '%sylius.behat.page.admin.promotion_coupon.create.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_promotion_coupon_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.promotion_coupon.generate', '%sylius.behat.page.admin.promotion_coupon.generate.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_promotion_coupon_generate'])
    ;

    $services
        ->set('sylius.behat.page.admin.promotion_coupon.index', '%sylius.behat.page.admin.promotion_coupon.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_promotion_coupon_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.promotion_coupon.update', '%sylius.behat.page.admin.promotion_coupon.update.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_promotion_coupon_update'])
    ;
};
