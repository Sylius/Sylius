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

use Sylius\Bundle\AdminBundle\CommandHandler\CreateAdminUserHandler;
use Sylius\Bundle\AdminBundle\Console\Command\ChangeAdminUserPasswordCommand;
use Sylius\Bundle\AdminBundle\Console\Command\CreateAdminUserCommand;
use Sylius\Bundle\AdminBundle\Console\Command\Factory\QuestionFactory;
use Sylius\Bundle\AdminBundle\Console\Command\Factory\QuestionFactoryInterface;
use Sylius\Bundle\AdminBundle\Context\AdminBasedLocaleContext;
use Sylius\Bundle\AdminBundle\Form\Type\AttributeType\Configuration\SelectAttributeConfigurationType;
use Sylius\Bundle\AdminBundle\Generator\TaxonSlugGenerator;
use Sylius\Bundle\AdminBundle\Generator\TaxonSlugGeneratorInterface;
use Sylius\Bundle\AdminBundle\Provider\LoggedInAdminUserProvider;
use Sylius\Bundle\AdminBundle\Provider\LoggedInAdminUserProviderInterface;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminUriBasedSectionResolver;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType as SelectAttributeFormType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/*.php');
    $container->import('services/product/*.php');
    $container->import('services/twig/*.php');

    $parameters->set('sylius_admin.command_handler.create_admin_user.validation_groups', ['sylius', 'sylius_user_create']);

    $services->set('sylius_admin.console.command.create_admin_user', CreateAdminUserCommand::class)
        ->public()
        ->args([
            service('sylius.command_bus'),
            '%sylius_locale.locale%',
            service('sylius_admin.console.command_factory.question'),
        ])
        ->tag('console.command');

    $services->set('sylius_admin.console.command.change_admin_user_password', ChangeAdminUserPasswordCommand::class)
        ->public()
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.security.password_updater'),
            service('sylius_admin.console.command_factory.question'),
        ])
        ->tag('console.command');

    $services->set('sylius_admin.command_handler.create_admin_user', CreateAdminUserHandler::class)
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.factory.admin_user'),
            service('sylius.canonicalizer'),
            service('validator'),
            '%sylius_admin.command_handler.create_admin_user.validation_groups%',
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_admin.console.command_factory.question', QuestionFactory::class);

    $services->alias(QuestionFactoryInterface::class, 'sylius_admin.console.command_factory.question');

    $services->set('sylius_admin.context.locale.admin_based', AdminBasedLocaleContext::class)
        ->args([service('security.token_storage')])
        ->tag('sylius.context.locale', ['priority' => 128]);

    $services->set('sylius_admin.section_resolver.admin_uri_based', AdminUriBasedSectionResolver::class)
        ->args(['/%sylius_admin.path_name%'])
        ->tag('sylius.uri_based_section_resolver', ['priority' => 20]);

    $services->alias('sylius.http_client', 'Psr\Http\Client\ClientInterface');

    $services->set('sylius.attribute_type.select', SelectAttributeType::class)
        ->tag('sylius.attribute.type', ['attribute_type' => 'select', 'label' => 'Select', 'form_type' => SelectAttributeFormType::class, 'configuration_form_type' => SelectAttributeConfigurationType::class]);

    $services->set('sylius_admin.generator.taxon_slug', TaxonSlugGenerator::class)
        ->args([service('sylius.generator.taxon_slug')]);

    $services->alias(TaxonSlugGeneratorInterface::class, 'sylius_admin.generator.taxon_slug');

    $services->set('sylius_admin.provider.logged_in_admin_user', LoggedInAdminUserProvider::class)
        ->args([
            service('security.helper'),
            service('security.token_storage'),
            service('request_stack'),
            service('sylius.repository.admin_user'),
        ]);

    $services->alias(LoggedInAdminUserProviderInterface::class, 'sylius_admin.provider.logged_in_admin_user');
};
