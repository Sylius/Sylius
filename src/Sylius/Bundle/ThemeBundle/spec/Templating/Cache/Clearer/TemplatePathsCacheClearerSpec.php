<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Templating\Cache\Clearer;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ClearableCache;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Templating\Cache\Clearer\TemplatePathsCacheClearer;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplatePathsCacheClearerSpec extends ObjectBehavior
{
    function let(Cache $cache)
    {
        $this->beConstructedWith($cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TemplatePathsCacheClearer::class);
    }

    function it_implements_cache_clearer_interface()
    {
        $this->shouldImplement(CacheClearerInterface::class);
    }

    function it_deletes_all_elements_if_cache_is_clearable(ClearableCache $cache)
    {
        $cache->deleteAll()->shouldBeCalled();

        $this->clear(null);
    }

    function it_does_not_throw_any_error_if_cache_is_not_clearable()
    {
        $this->clear(null);
    }
}
