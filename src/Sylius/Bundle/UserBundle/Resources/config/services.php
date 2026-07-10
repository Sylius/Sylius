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

use Sylius\Bundle\UserBundle\Authentication\AuthenticationFailureHandler;
use Sylius\Bundle\UserBundle\Authentication\AuthenticationSuccessHandler;
use Sylius\Bundle\UserBundle\Console\Command\DemoteUserCommand;
use Sylius\Bundle\UserBundle\Console\Command\PromoteUserCommand;
use Sylius\Bundle\UserBundle\Controller\SecurityController;
use Sylius\Bundle\UserBundle\EventListener\MailerListener;
use Sylius\Bundle\UserBundle\EventListener\PasswordUpdaterListener;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;
use Sylius\Bundle\UserBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\UserBundle\Form\Type\UserChangePasswordType;
use Sylius\Bundle\UserBundle\Form\Type\UserLoginType;
use Sylius\Bundle\UserBundle\Form\Type\UserRequestPasswordResetType;
use Sylius\Bundle\UserBundle\Form\Type\UserResetPasswordType;
use Sylius\Component\User\Canonicalizer\Canonicalizer;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Security\Checker\EnabledUserChecker;
use Sylius\Component\User\Security\PasswordUpdater;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.form.type.user_request_password_reset.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.user_reset_password.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.user_change_password.validation_groups', ['sylius']);

    $services
        ->set('sylius.console.command.demote_user', DemoteUserCommand::class)
        ->args([
            service('doctrine'),
            '%sylius.user.users%',
        ])
        ->public()
        ->tag('console.command')
    ;

    $services
        ->set('sylius.console.command.promote_user', PromoteUserCommand::class)
        ->args([
            service('doctrine'),
            '%sylius.user.users%',
        ])
        ->public()
        ->tag('console.command')
    ;

    $services
        ->set('sylius.authentication.success_handler', AuthenticationSuccessHandler::class)
        ->parent('security.authentication.success_handler')
    ;

    $services
        ->set('sylius.authentication.failure_handler', AuthenticationFailureHandler::class)
        ->parent('security.authentication.failure_handler')
        ->args([service('translator')])
    ;

    $services
        ->set('sylius.controller.user_security', SecurityController::class)
        ->args([
            service('security.authentication_utils'),
            service('form.factory'),
            service('twig'),
        ])
        ->public()
    ;

    $services->set('sylius.canonicalizer', Canonicalizer::class);
    $services->alias(CanonicalizerInterface::class, 'sylius.canonicalizer');

    $services
        ->set('sylius.security.password_updater', PasswordUpdater::class)
        ->args([service('security.user_password_hasher')])
    ;
    $services->alias(PasswordUpdaterInterface::class, 'sylius.security.password_updater');

    $services
        ->set('sylius.listener.password_updater', PasswordUpdaterListener::class)
        ->args([service('sylius.security.password_updater')])
        ->public()
        ->tag('kernel.event_listener', ['event' => 'sylius.user.pre_password_reset', 'method' => 'genericEventUpdater'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.pre_password_change', 'method' => 'genericEventUpdater'])
        ->tag('doctrine.event_listener', ['event' => 'prePersist', 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => 'preUpdate', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.user_mailer', MailerListener::class)
        ->args([service('sylius.email_sender')])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.password_reset.request.token', 'method' => 'sendResetPasswordTokenEmail'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.email_verification.token', 'method' => 'sendVerificationTokenEmail'])
    ;

    $services->set('sylius.form.type.user_login', UserLoginType::class)->tag('form.type');

    $services
        ->set('sylius.form.type.user_request_password_reset', UserRequestPasswordResetType::class)
        ->args([
            PasswordResetRequest::class,
            '%sylius.form.type.user_request_password_reset.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.user_reset_password', UserResetPasswordType::class)
        ->args([
            PasswordReset::class,
            '%sylius.form.type.user_reset_password.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.user_change_password', UserChangePasswordType::class)
        ->args([
            ChangePassword::class,
            '%sylius.form.type.user_change_password.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.user_checker.enabled', EnabledUserChecker::class)
        ->args([service('translator')])
        ->tag('security.user_checker.admin', ['priority' => 100])
        ->tag('security.user_checker.shop', ['priority' => 100])
        ->tag('security.user_checker.api_admin', ['priority' => 100])
        ->tag('security.user_checker.api_shop', ['priority' => 100])
    ;
};
