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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Bundle\ApiBundle\Exception\TranslationLocaleMismatchException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class TranslatableLocaleKeyDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_translatable_locale_key_denormalizer_already_called_for_%s';

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            !isset($context[self::getAlreadyCalledKey($type)]) &&
            is_array($data) &&
            is_a($type, TranslatableInterface::class, true)
        ;
    }

    /** @param array<string, array{ translations: array<array-key, mixed> }> $data */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $context[self::getAlreadyCalledKey($type)] = true;

        if (array_key_exists('translations', $data)) {
            foreach ($data['translations'] as $key => &$translation) {
                if (array_key_exists('locale', $translation) && $translation['locale'] !== $key) {
                    throw new TranslationLocaleMismatchException(sprintf(
                        'The locale of translation does not match the key. Key: "%s", locale: "%s"',
                        $key,
                        $translation['locale'],
                    ));
                }

                $translation['locale'] = $key;
            }
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    private static function getAlreadyCalledKey(string $class): string
    {
        return sprintf(self::ALREADY_CALLED, $class);
    }
}
