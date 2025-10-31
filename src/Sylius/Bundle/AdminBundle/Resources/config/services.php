<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/*.php');
    $container->import('services/product/*.php');
    $container->import('services/twig/*.php');
    
    $parameters->set('sylius_admin.command_handler.create_admin_user.validation_groups', ['sylius', 'sylius_user_create']);

    $services->set('sylius_admin.console.command.create_admin_user', 'Sylius\Bundle\AdminBundle\Console\Command\CreateAdminUserCommand')
        ->public()
        ->args([
            service('sylius.command_bus'),
            '%sylius_locale.locale%',
            service('sylius_admin.console.command_factory.question'),
        ])
        ->tag('console.command');

    $services->set('sylius_admin.console.command.change_admin_user_password', 'Sylius\Bundle\AdminBundle\Console\Command\ChangeAdminUserPasswordCommand')
        ->public()
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.security.password_updater'),
            service('sylius_admin.console.command_factory.question'),
        ])
        ->tag('console.command');

    $services->set('sylius_admin.command_handler.create_admin_user', 'Sylius\Bundle\AdminBundle\CommandHandler\CreateAdminUserHandler')
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.factory.admin_user'),
            service('sylius.canonicalizer'),
            service('validator'),
            '%sylius_admin.command_handler.create_admin_user.validation_groups%',
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_admin.console.command_factory.question', 'Sylius\Bundle\AdminBundle\Console\Command\Factory\QuestionFactory');

    $services->alias('Sylius\Bundle\AdminBundle\Console\Command\Factory\QuestionFactoryInterface', 'sylius_admin.console.command_factory.question');

    $services->set('sylius_admin.context.locale.admin_based', 'Sylius\Bundle\AdminBundle\Context\AdminBasedLocaleContext')
        ->args([service('security.token_storage')])
        ->tag('sylius.context.locale', ['priority' => 128]);

    $services->set('sylius_admin.section_resolver.admin_uri_based', 'Sylius\Bundle\AdminBundle\SectionResolver\AdminUriBasedSectionResolver')
        ->args(['/%sylius_admin.path_name%'])
        ->tag('sylius.uri_based_section_resolver', ['priority' => 20]);

    $services->alias('sylius.http_client', 'Psr\Http\Client\ClientInterface');

    $services->set('sylius.attribute_type.select', 'Sylius\Component\Attribute\AttributeType\SelectAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'select', 'label' => 'Select', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType', 'configuration_form_type' => 'Sylius\Bundle\AdminBundle\Form\Type\AttributeType\Configuration\SelectAttributeConfigurationType']);

    $services->set('sylius_admin.generator.taxon_slug', 'Sylius\Bundle\AdminBundle\Generator\TaxonSlugGenerator')
        ->args([service('sylius.generator.taxon_slug')]);

    $services->alias('Sylius\Bundle\AdminBundle\Generator\TaxonSlugGeneratorInterface', 'sylius_admin.generator.taxon_slug');

    $services->set('sylius_admin.provider.logged_in_admin_user', 'Sylius\Bundle\AdminBundle\Provider\LoggedInAdminUserProvider')
        ->args([
            service('security.helper'),
            service('security.token_storage'),
            service('request_stack'),
            service('sylius.repository.admin_user'),
        ]);

    $services->alias('Sylius\Bundle\AdminBundle\Provider\LoggedInAdminUserProviderInterface', 'sylius_admin.provider.logged_in_admin_user');
};
