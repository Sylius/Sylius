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

namespace Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use Symfony\Component\HttpFoundation\Request;

final class FakeChannelCodeProvider implements FakeChannelCodeProviderInterface
{
    public function getCode(Request $request): ?string
    {
        $queryChannelCode = $request->query->get('_channel_code');
        if (is_string($queryChannelCode) && $queryChannelCode !== '') {
            return $queryChannelCode;
        }

        $cookiesChannelCode = $request->cookies->get('_channel_code');
        if (is_string($cookiesChannelCode) && $cookiesChannelCode !== '') {
            return $cookiesChannelCode;
        }

        return null;
    }
}
