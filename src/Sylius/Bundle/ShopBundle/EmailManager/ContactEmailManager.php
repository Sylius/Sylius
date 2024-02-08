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

namespace Sylius\Bundle\ShopBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
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
        ?ChannelInterface $channel = null,
        ?string $localeCode = null,
    ): void {
        if ($channel === null) {
            @trigger_error(
                sprintf('Not passing channel into %s::%s is deprecated since Sylius 1.7', __CLASS__, __METHOD__),
                \E_USER_DEPRECATED,
            );
        }
        if ($localeCode === null) {
            @trigger_error(
                sprintf('Not passing locale code into %s::%s is deprecated since Sylius 1.7', __CLASS__, __METHOD__),
                \E_USER_DEPRECATED,
            );
        }

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
