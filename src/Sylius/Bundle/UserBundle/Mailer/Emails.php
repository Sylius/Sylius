<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Mailer;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Emails
{
    const RESET_PASSWORD_TOKEN = 'reset_password_token';
    const RESET_PASSWORD_PIN = 'reset_password_pin';
    const EMAIL_VERIFICATION_TOKEN = 'verification_token';
}
