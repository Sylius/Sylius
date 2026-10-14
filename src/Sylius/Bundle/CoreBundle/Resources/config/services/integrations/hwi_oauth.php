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

use Sylius\Bundle\CoreBundle\OAuth\Checker\AlwaysVerifiedEmailChecker;
use Sylius\Bundle\CoreBundle\OAuth\Checker\EmailVerificationCheckerInterface;
use Sylius\Bundle\CoreBundle\OAuth\Checker\ResponseDataEmailVerificationChecker;
use Sylius\Bundle\CoreBundle\OAuth\Checker\TrustedResourceOwnersEmailVerificationChecker;
use Sylius\Bundle\CoreBundle\OAuth\EventListener\UnverifiedEmailLoginFailureListener;
use Sylius\Bundle\CoreBundle\OAuth\UserProvider;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.oauth.user_provider', UserProvider::class)
        ->args([
            '%sylius.model.shop_user.class%',
            service('sylius.factory.customer'),
            service('sylius.factory.shop_user'),
            service('sylius.repository.shop_user'),
            service('sylius.factory.oauth_user'),
            service('sylius.repository.oauth_user'),
            service('sylius.manager.shop_user'),
            service('sylius.canonicalizer'),
            service('sylius.repository.customer'),
            service('sylius.oauth.email_verification_checker'),
        ])
        ->lazy()
    ;

    $services
        ->set('sylius.oauth.email_verification_checker.response_data', ResponseDataEmailVerificationChecker::class)
        ->args([
            service('logger'),
        ])
    ;

    $services
        ->set('sylius.oauth.email_verification_checker.trusted_resource_owners', TrustedResourceOwnersEmailVerificationChecker::class)
        ->args([
            service('sylius.oauth.email_verification_checker.response_data'),
            '%sylius_core.oauth.account_linking.trusted_resource_owners%',
        ])
    ;

    $services->set('sylius.oauth.email_verification_checker.always_verified', AlwaysVerifiedEmailChecker::class);

    $services->alias('sylius.oauth.email_verification_checker', 'sylius.oauth.email_verification_checker.trusted_resource_owners');

    $services->alias(EmailVerificationCheckerInterface::class, 'sylius.oauth.email_verification_checker');

    $services
        ->set('sylius.listener.oauth_unverified_email_login_failure', UnverifiedEmailLoginFailureListener::class)
        ->tag('kernel.event_listener', ['event' => LoginFailureEvent::class])
    ;
};
