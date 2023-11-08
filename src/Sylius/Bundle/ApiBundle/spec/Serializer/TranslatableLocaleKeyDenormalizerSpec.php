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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TranslatableLocaleKeyDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_translatable_locale_key_denormalizer_already_called';

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this
            ->supportsDenormalization([], TranslatableInterface::class, context: [self::ALREADY_CALLED => true])
            ->shouldReturn(false)
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

        $denormalizer->denormalize([], TranslatableInterface::class, null, [self::ALREADY_CALLED => true])->shouldHaveBeenCalledOnce();
    }

    function it_changes_keys_of_translations_to_locale(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $this->denormalize(
            [
                'translations' => [
                    'en' => ['locale' => 'en_US'],
                    ['locale' => 'de_DE'],
                    'fr' => ['slug' => 'slug'],
                ],
            ],
            TranslatableInterface::class,
        );

        $denormalizer->denormalize(
            [
                'translations' => [
                    'en_US' => ['locale' => 'en_US'],
                    'de_DE' => ['locale' => 'de_DE'],
                    '' => ['slug' => 'slug'],
                ],
            ],
            TranslatableInterface::class,
            null,
            [self::ALREADY_CALLED => true],
        )->shouldHaveBeenCalledOnce();
    }
}
