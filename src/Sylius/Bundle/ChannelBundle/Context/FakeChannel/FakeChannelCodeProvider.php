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

namespace Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use Symfony\Component\HttpFoundation\Request;

final class FakeChannelCodeProvider implements FakeChannelCodeProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCode(Request $request): ?string
    {
        return $request->query->get('_channel_code') ?: $request->cookies->get('_channel_code');
    }
}
