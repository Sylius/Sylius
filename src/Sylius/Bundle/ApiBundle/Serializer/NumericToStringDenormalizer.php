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

use Sylius\Bundle\ApiBundle\Serializer\Exception\InvalidAmountTypeException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class NumericToStringDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_numeric_to_string_denormalizer_already_called_for_%s';

    public function __construct(
        private readonly string $resourceClass,
        private readonly string $field,
    ) {
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            is_a($type, $this->resourceClass, true) &&
            !isset($context[self::getAlreadyCalledKey($type)]) &&
            is_array($data) &&
            isset($data[$this->field])
        ;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $context[self::getAlreadyCalledKey($type)] = true;

        $data = (array) $data;

        if (!is_numeric($data[$this->field])) {
            throw new InvalidAmountTypeException();
        }

        $data[$this->field] = (string) $data[$this->field];

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    private static function getAlreadyCalledKey(string $class): string
    {
        return sprintf(self::ALREADY_CALLED, $class);
    }
}
