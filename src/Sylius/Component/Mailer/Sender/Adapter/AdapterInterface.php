<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Sender\Adapter;

use Sylius\Component\Mailer\Model\EmailInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AdapterInterface
{
    /**
     * Send an e-mail.
     *
     * @param EmailInterface $email
     * @param array $recipients
     * @param array $data
     */
    public function send(EmailInterface $email, array $recipients, array $data = array());
}
