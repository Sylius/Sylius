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

namespace Sylius\Bundle\ShopBundle\EmailManager;

use Sylius\Component\Core\Model\ChannelInterface;

interface ContactEmailManagerInterface
{
    public function sendContactRequest(
        array $data,
        array $recipients,
        ?ChannelInterface $channel = null,
        ?string $localeCode = null
    ): void;
}
