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
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleCollectionProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository): void
    {
        $this->beConstructedWith($localeRepository);
    }

    function it_implements_locale_collection_provider_interface(): void
    {
        $this->shouldImplement(LocaleCollectionProviderInterface::class);
    }

    function it_returns_all_locales(
        RepositoryInterface $localeRepository,
        LocaleInterface $someLocale,
        LocaleInterface $anotherLocale,
    ): void {
        $someLocale->getCode()->willReturn('en_US');
        $anotherLocale->getCode()->willReturn('en_GB');
        $localeRepository->findAll()->willReturn([$someLocale, $anotherLocale]);

        $this->getAll()->shouldReturn(['en_US' => $someLocale, 'en_GB' => $anotherLocale]);
    }
}
