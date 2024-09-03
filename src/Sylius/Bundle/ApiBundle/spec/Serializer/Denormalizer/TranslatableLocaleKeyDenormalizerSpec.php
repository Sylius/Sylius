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
use Sylius\Bundle\ApiBundle\Exception\TranslationLocaleMismatchException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TranslatableLocaleKeyDenormalizerSpec extends ObjectBehavior
{
    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this
            ->supportsDenormalization([], TranslatableInterface::class, context: [
                'sylius_translatable_locale_key_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
            ])->shouldReturn(false)
        ;
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', TranslatableInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_translatable(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_does_nothing_if_there_is_no_translation_key(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $this->denormalize([], TranslatableInterface::class);

        $denormalizer->denormalize([], TranslatableInterface::class, null, [
            'sylius_translatable_locale_key_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true,
        ])->shouldHaveBeenCalledOnce();
    }

    function it_changes_keys_of_translations_to_locale(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $originalData = ['translations' => ['en_US' => ['locale' => 'en_US'], 'de_DE' => []]];
        $updatedData = ['translations' => ['en_US' => ['locale' => 'en_US'], 'de_DE' => ['locale' => 'de_DE']]];

        $this->denormalize($originalData, TranslatableInterface::class);

        $denormalizer->denormalize(
            $updatedData,
            TranslatableInterface::class,
            null,
            ['sylius_translatable_locale_key_denormalizer_already_called_for_Sylius\Component\Resource\Model\TranslatableInterface' => true],
        )->shouldHaveBeenCalledOnce();
    }

    function it_throws_an_exception_if_locale_is_not_the_same_as_key(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $this
            ->shouldThrow(TranslationLocaleMismatchException::class)
            ->during('denormalize', [['translations' => ['de_DE' => ['locale' => 'en_US']]], TranslatableInterface::class])
        ;
    }
}
