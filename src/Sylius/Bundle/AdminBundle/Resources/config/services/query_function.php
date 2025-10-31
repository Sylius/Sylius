<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.doctrine.query.taxon.all_taxons', 'Sylius\Bundle\AdminBundle\Doctrine\Query\Taxon\AllTaxons')
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius_admin.context.locale.admin_based'),
            service('sylius.translation_locale_provider'),
        ]);

    $services->alias('Sylius\Bundle\AdminBundle\Doctrine\Query\Taxon\AllTaxonsInterface', 'sylius_admin.doctrine.query.taxon.all_taxons');
};
