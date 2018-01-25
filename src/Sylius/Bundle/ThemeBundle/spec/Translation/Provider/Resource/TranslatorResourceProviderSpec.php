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
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResource;

final class TranslatorResourceProviderSpec extends ObjectBehavior
{
    function it_implements_translation_resource_provider_interface(): void
    {
        $this->shouldImplement(TranslatorResourceProviderInterface::class);
    }

    function it_transforms_previously_received_paths_into_translation_resources(): void
    {
        $this->beConstructedWith([
            'messages.en.yml',
            'domain.en.yml',
        ]);

        $this->getResources()->shouldBeLike([
            new TranslationResource('messages.en.yml'),
            new TranslationResource('domain.en.yml'),
        ]);
    }

    function it_extracts_unique_locales_from_received_paths(): void
    {
        $this->beConstructedWith([
            'messages.en.yml',
            'domain.en_US.yml',
            'validation.en.yml',
        ]);

        $this->getResourcesLocales()->shouldReturn([
            'en',
            'en_US',
        ]);
    }
}
