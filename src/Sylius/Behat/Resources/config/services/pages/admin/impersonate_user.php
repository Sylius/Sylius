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

use Sylius\Behat\Page\Admin\Administrator\ImpersonateUserPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.impersonate_user.class', ImpersonateUserPage::class);

    $services
        ->set('sylius.behat.page.admin.impersonate_user', '%sylius.behat.page.admin.impersonate_user.class%')
        ->parent('sylius.behat.symfony_page')
    ;
};
