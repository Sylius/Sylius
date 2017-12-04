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

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

final class CompositeTranslatorResourceProviderSpec extends ObjectBehavior
{
    function it_implements_translator_resource_provider_interface(): void
    {
        $this->shouldImplement(TranslatorResourceProviderInterface::class);
    }

    function it_aggregates_the_resources(
        TranslatorResourceProviderInterface $firstResourceProvider,
        TranslatorResourceProviderInterface $secondResourceProvider,
        TranslationResourceInterface $firstResource,
        TranslationResourceInterface $secondResource
    ): void {
        $this->beConstructedWith([$firstResourceProvider, $secondResourceProvider]);

        $firstResourceProvider->getResources()->willReturn([$firstResource]);
        $secondResourceProvider->getResources()->willReturn([$secondResource, $firstResource]);

        $this->getResources()->shouldReturn([$firstResource, $secondResource, $firstResource]);
    }

    function it_aggregates_the_resources_locales(
        TranslatorResourceProviderInterface $firstResourceProvider,
        TranslatorResourceProviderInterface $secondResourceProvider
    ): void {
        $this->beConstructedWith([$firstResourceProvider, $secondResourceProvider]);

        $firstResourceProvider->getResourcesLocales()->willReturn(['first-locale']);
        $secondResourceProvider->getResourcesLocales()->willReturn(['second-locale']);

        $this->getResourcesLocales()->shouldReturn(['first-locale', 'second-locale']);
    }

    function it_aggregates_the_unique_resources_locales(
        TranslatorResourceProviderInterface $firstResourceProvider,
        TranslatorResourceProviderInterface $secondResourceProvider
    ): void {
        $this->beConstructedWith([$firstResourceProvider, $secondResourceProvider]);

        $firstResourceProvider->getResourcesLocales()->willReturn(['first-locale']);
        $secondResourceProvider->getResourcesLocales()->willReturn(['second-locale', 'first-locale', 'second-locale']);

        $this->getResourcesLocales()->shouldReturn(['first-locale', 'second-locale']);
    }
}
