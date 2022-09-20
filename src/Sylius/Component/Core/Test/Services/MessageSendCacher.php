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

namespace Sylius\Component\Core\Test\Services;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;

final class MessageSendCacher implements EventSubscriberInterface
{
    public const CACHE_KEY = 'messages';

    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function onMessage(MessageEvent $event): void
    {
        if ($event->isQueued()) {
            return;
        }

        $item = $this->cache->getItem(self::CACHE_KEY);
        $messages = $item->isHit() ? $item->get() : [];
        $messages[] = $event->getMessage();
        $item->set($messages);

        $this->cache->save($item);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => ['onMessage', -1024],
        ];
    }
}
