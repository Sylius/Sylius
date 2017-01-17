<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Mailer;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class Emails
{
    const CONTACT_REQUEST = 'contact_request';
    const ORDER_CONFIRMATION = 'order_confirmation';
    const SHIPMENT_CONFIRMATION = 'shipment_confirmation';
    const USER_REGISTRATION = 'user_registration';

    private function __construct()
    {
    }
}
