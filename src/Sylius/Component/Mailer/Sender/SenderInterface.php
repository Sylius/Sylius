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

namespace Sylius\Component\Mailer\Sender;

interface SenderInterface
{
    public function send(string $code, array $recipients, array $data = [], array $attachments = [], array $replyTo = []): void;
}
