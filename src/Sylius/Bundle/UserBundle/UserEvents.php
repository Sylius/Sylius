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

final class UserEvents
{
    const REQUEST_RESET_PASSWORD_TOKEN = 'sylius.user.password_reset.request.token';

    const REQUEST_RESET_PASSWORD_PIN = 'sylius.user.password_reset.request.pin';

    const REQUEST_VERIFICATION_TOKEN = 'sylius.user.email_verification.token';

    const PRE_EMAIL_VERIFICATION = 'sylius.user.pre_email_verification';
    const POST_EMAIL_VERIFICATION = 'sylius.user.post_email_verification';

    const PRE_PASSWORD_RESET = 'sylius.user.pre_password_reset';
    const POST_PASSWORD_RESET = 'sylius.user.post_password_reset';

    const PRE_PASSWORD_CHANGE = 'sylius.user.pre_password_change';
    const POST_PASSWORD_CHANGE = 'sylius.user.post_password_change';

    /**
     * Occurs when the user is logged in programmatically.
     */
    const SECURITY_IMPLICIT_LOGIN = 'sylius.user.security.implicit_login';
}
