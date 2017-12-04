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

namespace Sylius\Bundle\UserBundle\Mailer;

final class Emails
{
    public const RESET_PASSWORD_TOKEN = 'reset_password_token';
    public const RESET_PASSWORD_PIN = 'reset_password_pin';
    public const EMAIL_VERIFICATION_TOKEN = 'verification_token';
}
