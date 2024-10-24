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

namespace Sylius\Bundle\ChannelBundle\Tests\Stub;

use Sylius\Bundle\ChannelBundle\Attribute\AsRequestBasedChannelResolver;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

#[AsRequestBasedChannelResolver(priority: 20)]
final class RequestBestChannelResolverStub implements RequestResolverInterface
{
    public function findChannel(Request $request): ?ChannelInterface
    {
        return null;
    }
}
