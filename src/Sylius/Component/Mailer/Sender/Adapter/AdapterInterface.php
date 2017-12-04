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

namespace Sylius\Component\Mailer\Sender\Adapter;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

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
     * @param array $replyTo
     */
    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = []
    ): void;
}
