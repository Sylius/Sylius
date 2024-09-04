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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TranslatableDenormalizerSpec extends ObjectBehavior
{
    function let(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $this->beConstructedWith($localeProvider);

        $this->setDenormalizer($denormalizer);
    }

    function it_only_supports_translatable_resource(): void
    {
        $this->supportsDenormalization([], TranslatableInterface::class, null, [
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'PUT',
        ])->shouldReturn(false);

        $this->supportsDenormalization([], TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldReturn(false);

        $this->supportsDenormalization([], \stdClass::class, null, [
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldReturn(false);
    }

    function it_does_nothing_when_data_contains_a_translation_in_default_locale(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $data = ['translations' => ['en' => ['locale' => 'en']]];

        $localeProvider->getDefaultLocaleCode()->willReturn('en');

        $denormalizer->denormalize($data, TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldBeCalled()->willReturn($data);

        $this
            ->denormalize($data, TranslatableInterface::class, null, [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'])
            ->shouldReturn($data)
        ;
    }

    function it_adds_default_translation_when_no_translations_passed_in_data(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en');

        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $denormalizer->denormalize($updatedData, TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldBeCalled()->willReturn($updatedData);

        $this
            ->denormalize([], TranslatableInterface::class, null, [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'])
            ->shouldReturn($updatedData)
        ;
    }

    function it_adds_default_translation_when_no_translation_passed_for_default_locale_in_data(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en');

        $originalData = ['translations' => ['en' => []]];
        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $denormalizer->denormalize($updatedData, TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldBeCalled()->willReturn($updatedData);

        $this
            ->denormalize($originalData, TranslatableInterface::class, null, [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'])
            ->shouldReturn($updatedData)
        ;
    }

    function it_adds_default_translation_when_passed_default_translation_has_empty_locale(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en');

        $originalData = ['translations' => ['en' => ['locale' => '']]];
        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $denormalizer->denormalize($updatedData, TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldBeCalled()->willReturn($updatedData);

        $this
            ->denormalize($originalData, TranslatableInterface::class, null, [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'])
            ->shouldReturn($updatedData)
        ;
    }

    function it_adds_default_translation_when_passed_default_translation_has_null_locale(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en');

        $originalData = ['translations' => ['en' => ['locale' => null]]];
        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $denormalizer->denormalize($updatedData, TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldBeCalled()->willReturn($updatedData);

        $this
            ->denormalize($originalData, TranslatableInterface::class, null, [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'])
            ->shouldReturn($updatedData)
        ;
    }

    function it_adds_default_translation_when_passed_default_translation_has_mismatched_locale(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en');

        $originalData = ['translations' => ['en' => ['locale' => 'fr']]];
        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $denormalizer->denormalize($updatedData, TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldBeCalled()->willReturn($updatedData);

        $this
            ->denormalize($originalData, TranslatableInterface::class, null, [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'])
            ->shouldReturn($updatedData)
        ;
    }

    function it_adds_default_translation_when_no_translation_in_default_locale_passed_in_data(
        DenormalizerInterface $denormalizer,
        TranslationLocaleProviderInterface $localeProvider,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en');

        $originalData = ['translations' => ['pl' => ['locale' => 'pl']]];
        $updatedData = ['translations' => ['en' => ['locale' => 'en'], 'pl' => ['locale' => 'pl']]];

        $denormalizer->denormalize($updatedData, TranslatableInterface::class, null, [
            'sylius_translatable_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
        ])->shouldBeCalled()->willReturn($updatedData);

        $this
            ->denormalize($originalData, TranslatableInterface::class, null, [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'])
            ->shouldReturn($updatedData)
        ;
    }
}
