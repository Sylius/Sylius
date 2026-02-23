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

use Sylius\Behat\Element\Admin\ProductAssociationType\FormElement;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Behat\Element\Admin\Product\AssociationsFormElement;
use Sylius\Behat\Element\Admin\Product\AttributesFormElement;
use Sylius\Behat\Element\Admin\Product\ChannelPricingsFormElement;
use Sylius\Behat\Element\Admin\Product\MediaFormElement;
use Sylius\Behat\Element\Admin\Product\TaxonomyFormElement;
use Sylius\Behat\Element\Admin\Product\TranslationsFormElement;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.behat.element.admin.product_association_type.form', FormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.product.association_form', AssociationsFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.product.attributes_form', AttributesFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.product.channel_pricing_form', ChannelPricingsFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.product.media_form', MediaFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.product.taxonomy_form', TaxonomyFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.product.translations_form', TranslationsFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;
};
