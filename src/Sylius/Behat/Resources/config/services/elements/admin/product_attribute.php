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

use Sylius\Behat\Element\Admin\ProductAttribute\FormElement;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Behat\Element\Admin\ProductAttribute\FilterElement;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.behat.element.admin.product_attribute.form', FormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.product_attribute.filter', FilterElement::class)
        ->parent('sylius.behat.element')
    ;
};
