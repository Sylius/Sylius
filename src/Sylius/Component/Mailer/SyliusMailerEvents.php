<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusMailerEvents
{
    const EMAIL_PRE_RENDER = 'sylius.email_rendered';
    const EMAIL_PRE_SEND = 'sylius.email_send.pre_send';
    const EMAIL_POST_SEND = 'sylius.email_send.post_send';
}
