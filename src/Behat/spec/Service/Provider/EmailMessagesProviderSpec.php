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

namespace spec\Sylius\Behat\Service\Provider;

use PhpSpec\ObjectBehavior;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Sylius\Behat\Service\MessageSendCacher;
use Sylius\Behat\Service\Provider\EmailMessagesProviderInterface;
use Symfony\Component\Mime\Email;

final class EmailMessagesProviderSpec extends ObjectBehavior
{
    function let(CacheItemPoolInterface $cacheItemPool): void
    {
        $this->beConstructedWith($cacheItemPool);
    }

    function it_implements_email_messages_provider_interface(): void
    {
        $this->shouldImplement(EmailMessagesProviderInterface::class);
    }

    function it_provides_email_messages(
        CacheItemPoolInterface $cacheItemPool,
        CacheItemInterface $cacheItem,
    ): void {
        $emailMessages = [new Email(), new Email(), new Email()];
        $cacheItem->get()->willReturn($emailMessages);

        $cacheItemPool->hasItem(MessageSendCacher::CACHE_KEY)->willReturn(true);
        $cacheItemPool->getItem(MessageSendCacher::CACHE_KEY)->willReturn($cacheItem);

        $this->provide()->shouldReturn($emailMessages);
    }

    function it_returns_an_empty_array_if_cache_key_does_not_exist(CacheItemPoolInterface $cacheItemPool): void
    {
        $cacheItemPool->hasItem(MessageSendCacher::CACHE_KEY)->willReturn(false);

        $this->provide()->shouldReturn([]);
    }
}
