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

use Sylius\Behat\Page\Admin\Account\ResetPasswordPage;
use Sylius\Behat\Page\Admin\Account\LoginPage;
use Sylius\Behat\Page\Admin\Account\RequestPasswordResetPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.reset_password', ResetPasswordPage::class);

    $services
        ->set('sylius.behat.page.admin.login', LoginPage::class)
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.shared_storage')])
    ;

    $services
        ->set('sylius.behat.page.admin.request_password_reset', RequestPasswordResetPage::class)
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.admin.reset_password', '%sylius.behat.page.admin.reset_password%')
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.shared_storage')])
    ;
};
