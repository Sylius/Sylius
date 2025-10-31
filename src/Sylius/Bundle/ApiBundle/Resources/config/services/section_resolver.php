<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.section_resolver.admin_api_uri_based', 'Sylius\Bundle\ApiBundle\SectionResolver\AdminApiUriBasedSectionResolver')
        ->args(['%sylius.security.api_admin_route%'])
        ->tag('sylius.uri_based_section_resolver', ['priority' => 30]);

    $services->set('sylius_api.section_resolver.shop_api_uri_based', 'Sylius\Bundle\ApiBundle\SectionResolver\ShopApiUriBasedSectionResolver')
        ->args([
            '%sylius.security.api_shop_route%',
            'orders',
        ])
        ->tag('sylius.uri_based_section_resolver', ['priority' => 40]);
};
