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

namespace Sylius\Bundle\CoreBundle\Mailer;

interface Emails
{
    public const CONTACT_REQUEST = 'contact_request';

    public const ORDER_CONFIRMATION = 'order_confirmation';

    public const ORDER_CONFIRMATION_RESENT = 'order_confirmation_resent';

    public const SHIPMENT_CONFIRMATION = 'shipment_confirmation';

    public const SHIPMENT_CONFIRMATION_RESENT = 'shipment_confirmation_resent';

    public const USER_REGISTRATION = 'user_registration';

    public const PASSWORD_RESET = 'password_reset';

    public const ADMIN_PASSWORD_RESET = 'admin_password_reset';

    public const ACCOUNT_VERIFICATION_TOKEN = 'account_verification_token';
}
