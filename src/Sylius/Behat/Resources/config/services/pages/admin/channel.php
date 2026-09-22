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

use Sylius\Behat\Page\Admin\Channel\CreatePage;
use Sylius\Behat\Page\Admin\Channel\IndexPage;
use Sylius\Behat\Page\Admin\Channel\UpdatePage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.channel.create.class', CreatePage::class);
    $parameters->set('sylius.behat.page.admin.channel.update.class', UpdatePage::class);
    $parameters->set('sylius.behat.page.admin.channel.index.class', IndexPage::class);

    $services
        ->set('sylius.behat.page.admin.channel.create', '%sylius.behat.page.admin.channel.create.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args([
            'sylius_admin_channel_create',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.channel.index', '%sylius.behat.page.admin.channel.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_channel_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.channel.update', '%sylius.behat.page.admin.channel.update.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args([
            'sylius_admin_channel_update',
            service(AutocompleteHelperInterface::class),
        ])
    ;
};
