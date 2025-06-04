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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Exception\TranslationLocaleMismatchException;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableLocaleKeyDenormalizer;
use Sylius\Resource\Model\TranslatableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TranslatableLocaleKeyDenormalizerTest extends TestCase
{
    private TranslatableLocaleKeyDenormalizer $translatableLocaleKeyDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translatableLocaleKeyDenormalizer = new TranslatableLocaleKeyDenormalizer();
    }

    public function testDoesNotSupportDenormalizationWhenTheDenormalizerHasAlreadyBeenCalled(): void
    {
        self::assertFalse(
            $this->translatableLocaleKeyDenormalizer->supportsDenormalization(
                [],
                TranslatableInterface::class,
                context: [
                    'sylius_translatable_locale_key_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                ],
            ),
        );
    }

    public function testDoesNotSupportDenormalizationWhenDataIsNotAnArray(): void
    {
        self::assertFalse(
            $this->translatableLocaleKeyDenormalizer->supportsDenormalization(
                'string',
                TranslatableInterface::class,
            ),
        );
    }

    public function testDoesNotSupportDenormalizationWhenTypeIsNotATranslatable(): void
    {
        self::assertFalse($this->translatableLocaleKeyDenormalizer->supportsDenormalization([], 'string'));
    }

    public function testDoesNothingIfThereIsNoTranslationKey(): void
    {
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $this->translatableLocaleKeyDenormalizer->setDenormalizer($denormalizerMock);

        $data = [];

        $denormalizerMock
            ->expects(self::once())
            ->method('denormalize')
            ->with(
                $data,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_locale_key_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                ],
            );

        $this->translatableLocaleKeyDenormalizer->denormalize($data, TranslatableInterface::class);
    }

    public function testChangesKeysOfTranslationsToLocale(): void
    {
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $this->translatableLocaleKeyDenormalizer->setDenormalizer($denormalizerMock);

        $originalData = [
            'translations' => [
                'en_US' => ['locale' => 'en_US'],
                'de_DE' => [],
            ],
        ];

        $expectedData = [
            'translations' => [
                'en_US' => ['locale' => 'en_US'],
                'de_DE' => ['locale' => 'de_DE'],
            ],
        ];

        $denormalizerMock
            ->expects(self::once())
            ->method('denormalize')
            ->with(
                $expectedData,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_locale_key_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                ],
            );

        $this->translatableLocaleKeyDenormalizer->denormalize($originalData, TranslatableInterface::class);
    }

    public function testThrowsAnExceptionIfLocaleIsNotTheSameAsKey(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $this->translatableLocaleKeyDenormalizer->setDenormalizer($denormalizerMock);

        self::expectException(TranslationLocaleMismatchException::class);

        $this->translatableLocaleKeyDenormalizer->denormalize(
            ['translations' => ['de_DE' => ['locale' => 'en_US']]],
            TranslatableInterface::class,
        );
    }
}
