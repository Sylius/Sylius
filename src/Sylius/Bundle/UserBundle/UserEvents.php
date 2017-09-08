<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle;

final class UserEvents
{
    public const REQUEST_RESET_PASSWORD_TOKEN = 'sylius.user.password_reset.request.token';

    public const REQUEST_RESET_PASSWORD_PIN = 'sylius.user.password_reset.request.pin';

    public const REQUEST_VERIFICATION_TOKEN = 'sylius.user.email_verification.token';

    public const PRE_EMAIL_VERIFICATION = 'sylius.user.pre_email_verification';
    public const POST_EMAIL_VERIFICATION = 'sylius.user.post_email_verification';

    public const PRE_PASSWORD_RESET = 'sylius.user.pre_password_reset';
    public const POST_PASSWORD_RESET = 'sylius.user.post_password_reset';

    public const PRE_PASSWORD_CHANGE = 'sylius.user.pre_password_change';
    public const POST_PASSWORD_CHANGE = 'sylius.user.post_password_change';

    public const SECURITY_IMPERSONATE = 'sylius.user.security.impersonate';

    /**
     * Occurs when the user is logged in programmatically.
     */
    public const SECURITY_IMPLICIT_LOGIN = 'sylius.user.security.implicit_login';
}
