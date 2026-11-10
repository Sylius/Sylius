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

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandArgumentsDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandDenormalizer;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Serializer;

/** @see https://github.com/Sylius/Sylius/issues/18883 */
final class CommandDenormalizersCacheabilityTest extends TestCase
{
    public function testCommandArgumentsDenormalizerIsConsultedForEveryContext(): void
    {
        $this->assertNotCachedAcrossContexts(new CommandArgumentsDenormalizer(
            $this->createMock(DenormalizerInterface::class),
            $this->createMock(IriToIdentifierConverterInterface::class),
        ));
    }

    public function testCommandDenormalizerIsConsultedForEveryContext(): void
    {
        $this->assertNotCachedAcrossContexts(new CommandDenormalizer(
            $this->createMock(DenormalizerInterface::class),
            $this->createMock(NameConverterInterface::class),
        ));
    }

    private function assertNotCachedAcrossContexts(DenormalizerInterface $denormalizer): void
    {
        $fallback = new class() implements DenormalizerInterface {
            public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
            {
                return true;
            }

            public function denormalize($data, $type, $format = null, array $context = []): string
            {
                return 'fallback';
            }

            public function getSupportedTypes(?string $format): array
            {
                return ['object' => true];
            }
        };

        $serializer = new Serializer([$denormalizer, $fallback]);

        $serializer->denormalize([], AddItemToCart::class, 'jsonld', ['input' => ['class' => AddItemToCart::class]]);

        $this->assertSame(
            'fallback',
            $serializer->denormalize([], AddItemToCart::class, 'jsonld', []),
            'The serializer reused a support decision taken for a different request context.',
        );
    }
}
