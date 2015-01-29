<?php

namespace Sylius\Component\Mailer\Sender;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SenderInterface
{
    /**
     * @param string $code
     * @param array  $recipients
     * @param array  $data
     */
    public function send($code, array $recipients, array $data = array());
}