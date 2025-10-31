<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.product.form', 'Sylius\Bundle\AdminBundle\Twig\Component\Product\FormComponent')
        ->args([
            service('sylius.repository.product'),
            service('form.factory'),
            '%sylius.model.product.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ProductType',
            service('sylius.generator.slug'),
            service('sylius.repository.product_attribute'),
            service('sylius.factory.product'),
        ])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product:form']);

    $services->set('sylius_admin.twig.component.product.product_attribute_autocomplete', 'Sylius\Bundle\AdminBundle\Twig\Component\Product\ProductAttributeAutocompleteComponent')
        ->args([service('ux.autocomplete.checksum_calculator')])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product:product_attribute_autocomplete']);

    $services->set('sylius_admin.twig.component.product.form.product_taxons', 'Sylius\Bundle\AdminBundle\Twig\Component\Product\Form\ProductTaxonsComponent')
        ->args([service('sylius_admin.doctrine.query.taxon.all_taxons')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:product:form:product_taxons']);
};
