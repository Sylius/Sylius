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

use Sylius\Behat\Page\Admin\ProductReview\IndexPage;
use Sylius\Behat\Page\Admin\ProductReview\UpdatePage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.product_review.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.product_review.update.class', UpdatePage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.product_review.index', '%sylius.behat.page.admin.product_review.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args([
            'sylius_admin_product_review_index',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.product_review.update', '%sylius.behat.page.admin.product_review.update.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_product_review_update'])
    ;
};
