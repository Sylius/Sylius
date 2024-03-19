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

use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class AddressDenormalizer implements ContextAwareDenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $objectNormalizer,
        private string $classType,
        private string $interfaceType,
    ) {
    }

    public function denormalize($data, $type, $format = null, array $context = [])
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
}
