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

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class DoctrineCollectionValuesNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const NORMALIZATION_CONTEXT_KEY = 'collection_values';

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return
            ($context[self::NORMALIZATION_CONTEXT_KEY] ?? false) &&
            $data instanceof Collection
        ;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        return $this->normalizer->normalize($object->getValues(), $format, $context);
    }
}
