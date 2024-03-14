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

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;

class AccountRegistrationEmailManager implements AccountRegistrationEmailManagerInterface
{
    public function __construct(private SenderInterface $emailSender)
    {
    }

    public function sendAccountRegistrationEmail(UserInterface $user, ChannelInterface $channel, string $localeCode): void
    {
        $this->emailSender->send(
            Emails::USER_REGISTRATION,
            [$user->getEmail()],
            [
                'user' => $user,
                'localeCode' => $localeCode,
                'channel' => $channel,
            ],
            [],
            [$user->getEmail()],
        );
    }
}
