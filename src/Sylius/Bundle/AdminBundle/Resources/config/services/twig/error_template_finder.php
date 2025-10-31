<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.error_template_finder', 'Sylius\Bundle\AdminBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinder')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_admin.provider.logged_in_admin_user'),
            service('twig'),
        ])
        ->tag('sylius.twig.error_template_finder');
};
