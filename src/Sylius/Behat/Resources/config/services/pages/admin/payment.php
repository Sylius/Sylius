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

use Sylius\Behat\Page\Admin\Payment\IndexPage as PaymentIndexPage;
use Sylius\Behat\Page\Admin\Payment\PaymentRequest\IndexPage;
use Sylius\Behat\Page\Admin\Payment\PaymentRequest\ShowPage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.payment.index.class', PaymentIndexPage::class);
    $parameters->set('sylius.behat.page.admin.payment.payment_request.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.payment.payment_request.show.class', ShowPage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.payment.index', '%sylius.behat.page.admin.payment.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_payment_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.payment.payment_request.index', '%sylius.behat.page.admin.payment.payment_request.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args([
            'sylius_admin_payment_request_index',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.payment.payment_request.show', '%sylius.behat.page.admin.payment.payment_request.show.class%')
        ->parent('sylius.behat.symfony_page')
    ;
};
