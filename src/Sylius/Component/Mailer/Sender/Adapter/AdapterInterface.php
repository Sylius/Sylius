<?php

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