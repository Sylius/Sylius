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

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class AddressDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $objectNormalizer,
        private string $classType,
        private string $interfaceType,
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        return $this->objectNormalizer->denormalize(
            $data,
            $this->classType,
            $format,
            $context,
        );
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $type === $this->interfaceType;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [$this->interfaceType => true];
    }
}
