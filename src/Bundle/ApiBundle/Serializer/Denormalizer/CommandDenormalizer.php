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

use Sylius\Bundle\ApiBundle\Exception\InvalidRequestArgumentException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $itemNormalizer,
        private NameConverterInterface $nameConverter,
    ) {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return isset($context['input']['class']);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        try {
            return $this->itemNormalizer->denormalize($data, $type, $format, $context);
        } catch (UnexpectedValueException $exception) {
            $previousException = $exception->getPrevious();
            if ($previousException instanceof NotNormalizableValueException) {
                throw new InvalidRequestArgumentException(
                    sprintf(
                        'Request field "%s" should be of type "%s".',
                        $this->normalizeFieldName($previousException->getPath(), $context['input']['class']),
                        implode(', ', $previousException->getExpectedTypes()),
                    ),
                );
            }

            throw $exception;
        } catch (MissingConstructorArgumentsException $exception) {
            $class = $context['input']['class'];

            throw new MissingConstructorArgumentsException(sprintf(
                'Request does not have the following required fields specified: %s.',
                implode(', ', array_map(
                    fn (string $field) => $this->normalizeFieldName($field, $class),
                    $exception->getMissingConstructorArguments(),
                )),
            ));
        }
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['object' => true];
    }

    private function normalizeFieldName(string $field, string $class): string
    {
        return $this->nameConverter->normalize($field, $class);
    }
}
