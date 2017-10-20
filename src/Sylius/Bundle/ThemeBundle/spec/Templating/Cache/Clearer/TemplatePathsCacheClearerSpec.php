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

namespace spec\Sylius\Bundle\ThemeBundle\Templating\Cache\Clearer;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ClearableCache;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

final class TemplatePathsCacheClearerSpec extends ObjectBehavior
{
    function let(Cache $cache): void
    {
        $this->beConstructedWith($cache);
    }

    function it_implements_cache_clearer_interface(): void
    {
        $this->shouldImplement(CacheClearerInterface::class);
    }

    function it_deletes_all_elements_if_cache_is_clearable(ClearableCache $cache): void
    {
        $cache->deleteAll()->shouldBeCalled();

        $this->clear(null);
    }

    function it_does_not_throw_any_error_if_cache_is_not_clearable(): void
    {
        $this->clear(null);
    }
}
