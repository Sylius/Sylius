<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class ContactEmailManager implements ContactEmailManagerInterface
{
    public function __construct(private SenderInterface $emailSender)
    {
    }

    public function sendContactRequest(
        array $data,
        array $recipients,
        ChannelInterface $channel,
        string $localeCode,
    ): void {
        $this->emailSender->send(
            Emails::CONTACT_REQUEST,
            $recipients,
            [
                'data' => $data,
                'channel' => $channel,
                'localeCode' => $localeCode,
            ],
            [],
            [$data['email']],
        );
    }
}
