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
use Sylius\Component\Mailer\Renderer\RenderedEmail;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface AdapterInterface
{
    /**
     * @param array  $recipients
     * @param string $senderAddress
     * @param string $senderName
     * @param RenderedEmail $renderedEmail
     * @param EmailInterface $email
     * @param array $data
     * @param array $attachments
     */
    public function send(
        array $recipients,
        $senderAddress,
        $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = []
    );
}
