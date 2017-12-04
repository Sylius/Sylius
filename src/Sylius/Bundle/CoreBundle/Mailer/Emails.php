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

namespace Sylius\Bundle\CoreBundle\Mailer;

final class Emails
{
    public const CONTACT_REQUEST = 'contact_request';
    public const ORDER_CONFIRMATION = 'order_confirmation';
    public const SHIPMENT_CONFIRMATION = 'shipment_confirmation';
    public const USER_REGISTRATION = 'user_registration';

    private function __construct()
    {
    }
}
