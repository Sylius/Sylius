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

trigger_deprecation(
    'sylius/shop-bundle',
    '1.13',
    'The "%s" class is deprecated, use "%s" instead.',
    ContactEmailManager::class,
    \Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager::class,
);

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see \Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager} instead. */
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
            trigger_deprecation(
                'sylius/shop-bundle',
                '1.7',
                'Not passing a $channel into %s::%s is deprecated.',
                __CLASS__,
                __METHOD__,
            );
        }
        if ($localeCode === null) {
            trigger_deprecation(
                'sylius/shop-bundle',
                '1.7',
                'Not passing a $localeCode into %s::%s is deprecated.',
                __CLASS__,
                __METHOD__,
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
