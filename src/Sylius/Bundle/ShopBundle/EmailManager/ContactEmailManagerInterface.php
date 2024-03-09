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

use Sylius\Component\Core\Model\ChannelInterface;

trigger_deprecation(
    'sylius/shop-bundle',
    '1.13',
    'The "%s" interface is deprecated, use "%s" instead.',
    ContactEmailManagerInterface::class,
    \Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface::class,
);

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see \Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface} instead. */
interface ContactEmailManagerInterface
{
    public function sendContactRequest(
        array $data,
        array $recipients,
        ?ChannelInterface $channel = null,
        ?string $localeCode = null,
    ): void;
}
