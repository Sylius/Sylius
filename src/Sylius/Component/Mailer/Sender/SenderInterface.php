<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Sender;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SenderInterface
{
    /**
     * @param string $code
     * @param array $recipients
     * @param array $data
     * @param array $attachments
     */
    public function send($code, array $recipients, array $data = [], array $attachments = []);
}
