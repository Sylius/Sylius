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

namespace Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TranslatableDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_translatable_denormalizer_already_called_for_%s';

    public function __construct(
        private TranslationLocaleProviderInterface $localeProvider,
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[self::getAlreadyCalledKey($type)] = true;

        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        if (!$this->hasDefaultTranslation($data['translations'] ?? [], $defaultLocaleCode)) {
            $data['translations'][$defaultLocaleCode] = [
                'locale' => $defaultLocaleCode,
            ];
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            Request::METHOD_POST === ($context[ContextKeys::HTTP_REQUEST_METHOD_TYPE] ?? null) &&
            !isset($context[self::getAlreadyCalledKey($type)]) &&
            is_a($type, TranslatableInterface::class, true)
        ;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [TranslatableInterface::class => false];
    }

    private static function getAlreadyCalledKey(string $class): string
    {
        return sprintf(self::ALREADY_CALLED, $class);
    }

    private function hasDefaultTranslation(array $translations, string $defaultLocale): bool
    {
        return
            isset($translations[$defaultLocale]['locale']) &&
            $defaultLocale === $translations[$defaultLocale]['locale']
        ;
    }
}
