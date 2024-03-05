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

namespace spec\Sylius\Bundle\LocaleBundle\Doctrine\EventListener;

use PhpSpec\ObjectBehavior;
use Symfony\Contracts\Cache\CacheInterface;

final class LocaleModificationListenerSpec extends ObjectBehavior
{
    function let(CacheInterface $cache): void
    {
        $this->beConstructedWith($cache);
    }

    function it_invalidates_cache(CacheInterface $cache): void
    {
        $cache->delete('sylius_locales')->shouldBeCalled();

        $this->invalidateCachedLocales();
    }
}
