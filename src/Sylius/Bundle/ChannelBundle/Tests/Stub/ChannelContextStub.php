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

use Sylius\Bundle\ChannelBundle\Attribute\AsChannelContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\Channel;
use Sylius\Component\Channel\Model\ChannelInterface;

#[AsChannelContext(priority: 15)]
final class ChannelContextStub implements ChannelContextInterface
{
    public function getChannel(): ChannelInterface
    {
        return new Channel();
    }
}
