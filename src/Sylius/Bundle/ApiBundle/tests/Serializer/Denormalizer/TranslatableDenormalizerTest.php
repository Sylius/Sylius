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
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableDenormalizer;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TranslatableDenormalizerTest extends TestCase
{
    private DenormalizerInterface&MockObject $denormalizer;

    private MockObject&TranslationLocaleProviderInterface $localeProvider;

    private TranslatableDenormalizer $translatableDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->localeProvider = $this->createMock(TranslationLocaleProviderInterface::class);
        $this->translatableDenormalizer = new TranslatableDenormalizer($this->localeProvider);
        $this->translatableDenormalizer->setDenormalizer($this->denormalizer);
    }

    public function testOnlySupportsTranslatableResource(): void
    {
        self::assertFalse(
            $this->translatableDenormalizer->supportsDenormalization(
                [],
                TranslatableInterface::class,
                null,
                [
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'PUT',
                ],
            ),
        );

        self::assertFalse(
            $this->translatableDenormalizer->supportsDenormalization(
                [],
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            ),
        );

        self::assertFalse(
            $this->translatableDenormalizer->supportsDenormalization(
                [],
                \stdClass::class,
                null,
                [
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            ),
        );
    }

    public function testDoesNothingWhenDataContainsATranslationInDefaultLocale(): void
    {
        $data = ['translations' => ['en' => ['locale' => 'en']]];

        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en');

        $this->denormalizer->expects(
            self::once(),
        )
            ->method('denormalize')
            ->with(
                $data,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            )
            ->willReturn($data);

        self::assertSame(
            $data,
            $this->translatableDenormalizer->denormalize(
                $data,
                TranslatableInterface::class,
                null,
                [
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            ),
        );
    }

    public function testAddsDefaultTranslationWhenNoTranslationsPassedInData(): void
    {
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en');

        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with(
                $updatedData,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            )
            ->willReturn($updatedData);

        self::assertSame(
            $updatedData,
            $this->translatableDenormalizer->denormalize(
                [],
                TranslatableInterface::class,
                null,
                [
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            ),
        );
    }

    public function testAddsDefaultTranslationWhenNoTranslationPassedForDefaultLocaleInData(): void
    {
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en');

        $originalData = ['translations' => ['en' => []]];

        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with(
                $updatedData,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            )
            ->willReturn($updatedData);

        self::assertSame(
            $updatedData,
            $this->translatableDenormalizer->denormalize(
                $originalData,
                TranslatableInterface::class,
                null,
                [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'],
            ),
        );
    }

    public function testAddsDefaultTranslationWhenPassedDefaultTranslationHasEmptyLocale(): void
    {
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en');

        $originalData = ['translations' => ['en' => ['locale' => '']]];

        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with(
                $updatedData,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            )
            ->willReturn($updatedData);

        self::assertSame(
            $updatedData,
            $this->translatableDenormalizer->denormalize(
                $originalData,
                TranslatableInterface::class,
                null,
                [ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST'],
            ),
        );
    }

    public function testAddsDefaultTranslationWhenPassedDefaultTranslationHasNullLocale(): void
    {
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en');

        $originalData = ['translations' => ['en' => ['locale' => null]]];

        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with(
                $updatedData,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            )
            ->willReturn($updatedData);

        self::assertSame(
            $updatedData,
            $this->translatableDenormalizer->denormalize(
                $originalData,
                TranslatableInterface::class,
                null,
                [
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            ),
        );
    }

    public function testAddsDefaultTranslationWhenPassedDefaultTranslationHasMismatchedLocale(): void
    {
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en');

        $originalData = ['translations' => ['en' => ['locale' => 'fr']]];

        $updatedData = ['translations' => ['en' => ['locale' => 'en']]];

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with(
                $updatedData,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            )
            ->willReturn($updatedData);

        self::assertSame(
            $updatedData,
            $this->translatableDenormalizer->denormalize(
                $originalData,
                TranslatableInterface::class,
                null,
                [
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            ),
        );
    }

    public function testAddsDefaultTranslationWhenNoTranslationInDefaultLocalePassedInData(): void
    {
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en');

        $originalData = ['translations' => ['pl' => ['locale' => 'pl']]];

        $updatedData = ['translations' => ['en' => ['locale' => 'en'], 'pl' => ['locale' => 'pl']]];

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with(
                $updatedData,
                TranslatableInterface::class,
                null,
                [
                    'sylius_translatable_denormalizer_already_called_for_Sylius\Resource\Model\TranslatableInterface' => true,
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            )
            ->willReturn($updatedData);

        self::assertSame(
            $updatedData,
            $this->translatableDenormalizer->denormalize(
                $originalData,
                TranslatableInterface::class,
                null,
                [
                    ContextKeys::HTTP_REQUEST_METHOD_TYPE => 'POST',
                ],
            ),
        );
    }
}
