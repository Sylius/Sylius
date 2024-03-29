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

namespace spec\Sylius\Component\Locale\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class CachedLocaleCollectionProviderSpec extends ObjectBehavior
{
    function let(LocaleCollectionProviderInterface $decorated, CacheInterface $cache): void
    {
        $this->beConstructedWith($decorated, $cache);
    }

    function it_implements_locale_collection_provider_interface(): void
    {
        $this->shouldImplement(LocaleCollectionProviderInterface::class);
    }

    function it_returns_all_locales_via_cache(
        LocaleCollectionProviderInterface $decorated,
        CacheInterface $cache,
        LocaleInterface $someLocale,
        LocaleInterface $anotherLocale,
    ): void {
        $someLocale->getCode()->willReturn('en_US');
        $anotherLocale->getCode()->willReturn('en_GB');

        $cache->get('sylius_locales', Argument::type('callable'))->will(function ($args) {
            return $args[1]();
        });
        $decorated->getAll()->willReturn(['en_US' => $someLocale, 'en_GB' => $anotherLocale]);

        $this->getAll()->shouldReturn(['en_US' => $someLocale, 'en_GB' => $anotherLocale]);
    }
}
