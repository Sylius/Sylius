<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle;

/**
 * Contains all events thrown in the SyliusUserBundle.
 */
final class UserEvents
{
    /**
     * The REQUEST_RESET_PASSWORD_TOKEN event occurs when the resetting process is initialized and user requested for confirmation token.
     *
     * This event allows you to send mail with verification token.
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent instance.
     */
    const REQUEST_RESET_PASSWORD_TOKEN = 'sylius.user.password_reset.request.token';

    /**
     * The REQUEST_RESET_PASSWORD_PIN event occurs when the resetting process is initialized and user requested for confirmation pin.
     *
     * This event allows you to send mail with verification pin.
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent instance.
     */
    const REQUEST_RESET_PASSWORD_PIN = 'sylius.user.password_reset.request.pin';

    /**
     * The PRE_PASSWORD_RESET event occurs right before the user changes are flushed.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent instance.
     */
    const PRE_PASSWORD_RESET = 'sylius.user.pre_password_reset';

    /**
     * The POST_PASSWORD_RESET event occurs right after the user changes are flushed.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent instance.
     */
    const POST_PASSWORD_RESET = 'sylius.user.post_password_reset';

    /**
     * The PRE_PASSWORD_CHANGE event occurs before the change form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent instance.
     */
    const PRE_PASSWORD_CHANGE = 'sylius.user.pre_password_change';

    /**
     * The POST_PASSWORD_CHANGE event occurs after the change form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent instance.
     */
    const POST_PASSWORD_CHANGE = 'sylius.user.post_password_change';

    /**
     * The SECURITY_IMPLICIT_LOGIN event occurs when the user is logged in programmatically.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a Sylius\Bundle\UserBundle\Event\UserEvent instance.
     */
    const SECURITY_IMPLICIT_LOGIN = 'sylius.user.security.implicit_login';
}
