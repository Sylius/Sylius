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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\AdminBundle\Form\Type\ProductType;
use Sylius\Bundle\AdminBundle\Twig\Component\Product\Form\ProductTaxonsComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Product\FormComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Product\ProductAttributeAutocompleteComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.twig.component.product.form', FormComponent::class)
        ->args([
            service('sylius.repository.product'),
            service('form.factory'),
            '%sylius.model.product.class%',
            ProductType::class,
            service('sylius.generator.slug'),
            service('sylius.repository.product_attribute'),
            service('sylius.factory.product'),
        ])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product:form'])
    ;

    $services
        ->set('sylius_admin.twig.component.product.product_attribute_autocomplete', ProductAttributeAutocompleteComponent::class)
        ->args([service('ux.autocomplete.checksum_calculator')])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product:product_attribute_autocomplete'])
    ;

    $services
        ->set('sylius_admin.twig.component.product.form.product_taxons', ProductTaxonsComponent::class)
        ->args([service('sylius_admin.doctrine.query.taxon.all_taxons')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:product:form:product_taxons'])
    ;
};
