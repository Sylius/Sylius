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

use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Element\BrowserElement;
use Sylius\Behat\Element\SaveElement;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.behat.element', Element::class)
        ->abstract()
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ])
    ;

    $services
        ->set('sylius.behat.element.browser', BrowserElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.save', SaveElement::class)
        ->parent('sylius.behat.element')
    ;
};
