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

use Psr\Clock\ClockInterface as PsrClockInterface;
use Sylius\Behat\Service\Accessor\NotificationAccessor;
use Sylius\Behat\Service\Accessor\TableAccessor;
use Sylius\Behat\Service\ApiSecurityService;
use Sylius\Behat\Service\Checker\EmailChecker;
use Sylius\Behat\Service\Checker\ImageExistenceChecker;
use Sylius\Behat\Service\Clock;
use Sylius\Behat\Service\Context\GuestCartContext;
use Sylius\Behat\Service\Converter\IriConverter;
use Sylius\Behat\Service\Factory\AddressFactory;
use Sylius\Behat\Service\Helper\AutocompleteHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Behat\Service\Helper\JavaScriptTestHelper;
use Sylius\Behat\Service\MessageSendCacher;
use Sylius\Behat\Service\NotificationChecker;
use Sylius\Behat\Service\PaymentRequest\CommandHandler\Offline\NotifyPaymentRequestHandler;
use Sylius\Behat\Service\PaymentRequest\CommandProvider\Offline\NotifyPaymentRequestCommandProvider;
use Sylius\Behat\Service\PaymentRequest\Provider\DummyNotifyPaymentProvider;
use Sylius\Behat\Service\Provider\EmailMessagesProvider;
use Sylius\Behat\Service\Provider\EmailMessagesProviderInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolver;
use Sylius\Behat\Service\ResponseLoader;
use Sylius\Behat\Service\SecurityService;
use Sylius\Behat\Service\SessionManager;
use Sylius\Behat\Service\SessionManagerInterface;
use Sylius\Behat\Service\Setter\ChannelContextSetter;
use Sylius\Behat\Service\Setter\CookieSetter;
use Sylius\Behat\Service\SharedSecurityService;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Symfony\Component\Clock\ClockInterface as SymfonyClockInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $container->import('services/api.php');
    $container->import('services/contexts.php');
    $container->import('services/elements/**/*.php');
    $container->import('services/pages.php');

    $parameters->set('sylius.behat.clock.date_file', '%kernel.project_dir%/var/date.txt');
    $parameters->set('sylius.behat.guest_cart_token_file', '%kernel.project_dir%/var/guest_cart_token.txt');
    $parameters->set('sylius.behat.notification_accessor.admin.locator', '[data-test-sylius-flash-message]');
    $parameters->set('sylius.behat.notification_accessor.shop.locator', '[data-test-sylius-flash-message]');
    $parameters->set('sylius.behat.notification_checker.admin.class_map', [
        'failure' => 'alert-danger',
        'error' => 'alert-danger',
        'info' => 'alert-info',
        'success' => 'alert-success',
    ]);
    $parameters->set('sylius.behat.notification_checker.shop.class_map', [
        'failure' => 'alert-danger',
        'error' => 'alert-danger',
        'info' => 'alert-info',
        'success' => 'alert-success',
    ]);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.cookie_setter', CookieSetter::class)
        ->private()
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ])
    ;

    $services
        ->set('sylius.behat.channel_context_setter', ChannelContextSetter::class)
        ->private()
        ->args([service('sylius.behat.cookie_setter')])
    ;

    $services
        ->set('sylius.behat.admin_security', SecurityService::class)
        ->private()
        ->args([
            service('request_stack'),
            service('sylius.behat.cookie_setter'),
            'admin',
            service('session.factory')->nullOnInvalid(),
        ])
    ;

    $services
        ->set('sylius.behat.api_admin_security', ApiSecurityService::class)
        ->public()
        ->args([
            service('sylius.behat.shared_storage'),
            service('lexik_jwt_authentication.jwt_manager'),
            'api_admin',
        ])
    ;

    $services
        ->set('sylius.behat.api_shop_security', ApiSecurityService::class)
        ->public()
        ->args([
            service('sylius.behat.shared_storage'),
            service('lexik_jwt_authentication.jwt_manager'),
            'api_shop',
        ])
    ;

    $services
        ->set('sylius.behat.shop_security', SecurityService::class)
        ->private()
        ->args([
            service('request_stack'),
            service('sylius.behat.cookie_setter'),
            'shop',
            service('session.factory')->nullOnInvalid(),
        ])
    ;

    $services
        ->set(SessionManagerInterface::class, SessionManager::class)
        ->private()
        ->args([
            service('behat.mink'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.shop_security'),
        ])
    ;

    $services
        ->set('sylius.behat.shared_security', SharedSecurityService::class)
        ->private()
        ->args([service('sylius.behat.admin_security')])
    ;

    $services
        ->set('sylius.behat.api.shared_security', SharedSecurityService::class)
        ->private()
        ->args([service('sylius.behat.api_admin_security')])
    ;

    $services
        ->set('sylius.behat.table_accessor', TableAccessor::class)
        ->private()
    ;

    $services
        ->set('sylius.behat.checker.image_existence', ImageExistenceChecker::class)
        ->args([
            service('sylius.liip.filter_service'),
            '%sylius_core.public_dir%',
        ])
    ;

    $services
        ->set('sylius.behat.response_loader', ResponseLoader::class)
        ->private()
    ;

    $services
        ->set('sylius.behat.notification_accessor.admin', NotificationAccessor::class)
        ->private()
        ->args([
            service('behat.mink.default_session'),
            '%sylius.behat.notification_accessor.admin.locator%',
        ])
    ;

    $services
        ->set('sylius.behat.notification_accessor.shop', NotificationAccessor::class)
        ->private()
        ->args([
            service('behat.mink.default_session'),
            '%sylius.behat.notification_accessor.shop.locator%',
        ])
    ;

    $services
        ->set('sylius.behat.notification_checker.admin', NotificationChecker::class)
        ->private()
        ->args([
            service('sylius.behat.notification_accessor.admin'),
            '%sylius.behat.notification_checker.admin.class_map%',
        ])
    ;

    $services
        ->set('sylius.behat.notification_checker.shop', NotificationChecker::class)
        ->private()
        ->args([
            service('sylius.behat.notification_accessor.shop'),
            '%sylius.behat.notification_checker.shop.class_map%',
        ])
    ;

    $services
        ->set('sylius.behat.current_page_resolver', CurrentPageResolver::class)
        ->private()
        ->args([
            service('behat.mink.default_session'),
            service('router'),
        ])
    ;

    $services
        ->set('sylius.behat.shared_storage', SharedStorage::class)
        ->private()
    ;

    $services->set(AutocompleteHelperInterface::class, AutocompleteHelper::class);

    $services
        ->set('sylius.behat.java_script_test_helper', JavaScriptTestHelper::class)
        ->args([
            1000000,
            7,
        ])
    ;

    $services
        ->set('sylius.behat.email_checker', EmailChecker::class)
        ->args([service(EmailMessagesProviderInterface::class)])
    ;

    $services
        ->set(EmailMessagesProviderInterface::class, EmailMessagesProvider::class)
        ->args([service('test.mailer_pool')])
    ;

    $services
        ->set('sylius.behat.message_send_cacher', MessageSendCacher::class)
        ->args([service('test.mailer_pool')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius.behat.clock', Clock::class)
        ->args(['%sylius.behat.clock.date_file%'])
    ;

    $services->alias('clock', 'sylius.behat.clock');

    $services->alias(SymfonyClockInterface::class, 'sylius.behat.clock');

    $services->alias(PsrClockInterface::class, 'sylius.behat.clock');

    $services->alias('argument_resolver.datetime', 'sylius.behat.clock');

    $services
        ->set(IriConverter::class)
        ->private()
        ->decorate('api_platform.symfony.iri_converter', null, 32)
        ->args([
            service('.inner'),
            service(OperationResolverInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.factory.address', AddressFactory::class)
        ->decorate('sylius.custom_factory.address')
        ->args([service('.inner')]);

    $services
        ->set(DummyNotifyPaymentProvider::class)
        ->autoconfigure()
        ->args([service('sylius.repository.payment')])
    ;

    $services
        ->set(NotifyPaymentRequestCommandProvider::class)
        ->tag('sylius.command_provider.payment_request.offline', ['action' => 'notify'])
    ;

    $services
        ->set(NotifyPaymentRequestHandler::class)
        ->args([
            service('sylius.provider.payment_request'),
            service('sylius_abstraction.state_machine'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.payment_request.command_bus'])
    ;

    $services
        ->set('sylius.behat.context.guest_cart', GuestCartContext::class)
        ->args([
            service('sylius.repository.order'),
            '%sylius.behat.guest_cart_token_file%',
        ])
        ->tag('sylius.context.cart', ['priority' => -900])
    ;
};
