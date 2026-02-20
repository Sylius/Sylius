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

use Sylius\Behat\Page\Shop\Account\AddressBook\CreatePage;
use Sylius\Behat\Page\Shop\Account\AddressBook\IndexPage;
use Sylius\Behat\Page\Shop\Account\AddressBook\UpdatePage;
use Sylius\Behat\Page\Shop\Account\ChangePasswordPage;
use Sylius\Behat\Page\Shop\Account\DashboardPage;
use Sylius\Behat\Page\Shop\Account\LoginPage;
use Sylius\Behat\Page\Shop\Account\Order\IndexPage as OrderIndexPage;
use Sylius\Behat\Page\Shop\Account\Order\ShowPage as OrderShowPage;
use Sylius\Behat\Page\Shop\Account\ProfileUpdatePage;
use Sylius\Behat\Page\Shop\Account\RegisterPage;
use Sylius\Behat\Page\Shop\Account\RegisterThankYouPage;
use Sylius\Behat\Page\Shop\Account\RequestPasswordResetPage;
use Sylius\Behat\Page\Shop\Account\ResetPasswordPage;
use Sylius\Behat\Page\Shop\Account\VerificationPage;
use Sylius\Behat\Page\Shop\Account\WellKnownPasswordChangePage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.shop.account.address_book.create.class', CreatePage::class);
    $parameters->set('sylius.behat.page.shop.account.address_book.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.shop.account.address_book.update.class', UpdatePage::class);
    $parameters->set('sylius.behat.page.shop.account.change_password.class', ChangePasswordPage::class);
    $parameters->set('sylius.behat.page.shop.account.dashboard.class', DashboardPage::class);
    $parameters->set('sylius.behat.page.shop.account.login.class', LoginPage::class);
    $parameters->set('sylius.behat.page.shop.account.order.index.class', OrderIndexPage::class);
    $parameters->set('sylius.behat.page.shop.account.order.show.class', OrderShowPage::class);
    $parameters->set('sylius.behat.page.shop.account.profile_update.class', ProfileUpdatePage::class);
    $parameters->set('sylius.behat.page.shop.account.register.class', RegisterPage::class);
    $parameters->set('sylius.behat.page.shop.account.register.thank_you.class', RegisterThankYouPage::class);
    $parameters->set('sylius.behat.page.shop.account.request_password_reset.class', RequestPasswordResetPage::class);
    $parameters->set('sylius.behat.page.shop.account.reset_password.class', ResetPasswordPage::class);
    $parameters->set('sylius.behat.page.shop.account.verify.class', VerificationPage::class);
    $parameters->set('sylius.behat.page.shop.account.well_known_password_change.class', WellKnownPasswordChangePage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.shop.account.address_book.create', '%sylius.behat.page.shop.account.address_book.create.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.address_book.index', '%sylius.behat.page.shop.account.address_book.index.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.address_book.update', '%sylius.behat.page.shop.account.address_book.update.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.change_password', '%sylius.behat.page.shop.account.change_password.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.shared_storage')])
    ;

    $services
        ->set('sylius.behat.page.shop.account.dashboard', '%sylius.behat.page.shop.account.dashboard.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.login', '%sylius.behat.page.shop.account.login.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([
            service('sylius.behat.table_accessor'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.page.shop.account.order.index', '%sylius.behat.page.shop.account.order.index.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')])
    ;

    $services
        ->set('sylius.behat.page.shop.account.order.show', '%sylius.behat.page.shop.account.order.show.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')])
    ;

    $services
        ->set('sylius.behat.page.shop.account.profile_update', '%sylius.behat.page.shop.account.profile_update.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.register', '%sylius.behat.page.shop.account.register.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.register.thank_you', '%sylius.behat.page.shop.account.register.thank_you.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.request_password_reset', '%sylius.behat.page.shop.account.request_password_reset.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.reset_password', '%sylius.behat.page.shop.account.reset_password.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.shared_storage')])
    ;

    $services
        ->set('sylius.behat.page.shop.account.verify', '%sylius.behat.page.shop.account.verify.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.account.well_known_password_change', '%sylius.behat.page.shop.account.well_known_password_change.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;
};
