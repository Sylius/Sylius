<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.taxon.delete', 'Sylius\Bundle\AdminBundle\Twig\Component\Taxon\DeleteComponent')
        ->args([service('security.csrf.token_manager')])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:taxon:delete']);

    $services->set('sylius_admin.twig.component.taxon.form', 'Sylius\Bundle\AdminBundle\Twig\Component\Taxon\FormComponent')
        ->args([
            service('sylius.repository.taxon'),
            service('form.factory'),
            '%sylius.model.taxon.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\TaxonType',
            service('sylius_admin.generator.taxon_slug'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:taxon:form']);

    $services->set('sylius_admin.twig.component.taxon.tree', 'Sylius\Bundle\AdminBundle\Twig\Component\Taxon\TreeComponent')
        ->args([
            service('sylius_admin.doctrine.query.taxon.all_taxons'),
            service('sylius.repository.taxon'),
            service('sylius.manager.taxon'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:taxon:tree']);
};
