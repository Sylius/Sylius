<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.creator.avatar_image', 'Sylius\Bundle\ApiBundle\Creator\AvatarImageCreator')
        ->args([
            service('sylius.factory.avatar_image'),
            service('sylius.repository.admin_user'),
            service('sylius.uploader.image'),
            service('api_platform.symfony.iri_converter'),
        ]);

    $services->set('sylius_api.creator.product_image', 'Sylius\Bundle\ApiBundle\Creator\ProductImageCreator')
        ->args([
            service('sylius.factory.product_image'),
            service('sylius.repository.product'),
            service('sylius.uploader.image'),
            service('api_platform.symfony.iri_converter'),
        ]);

    $services->set('sylius_api.creator.taxon_image', 'Sylius\Bundle\ApiBundle\Creator\TaxonImageCreator')
        ->args([
            service('sylius.factory.taxon_image'),
            service('sylius.repository.taxon'),
            service('sylius.uploader.image'),
        ]);
};
