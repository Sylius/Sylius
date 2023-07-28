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

use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/** @experimental */
final class CommandDenormalizer implements ContextAwareDenormalizerInterface
{
    private const OBJECT_TO_POPULATE = 'object_to_populate';

    public function __construct(
        private DenormalizerInterface $itemNormalizer,
        private NameConverterInterface $nameConverter,
    ) {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return isset($context['input']['class']);
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (isset($context[self::OBJECT_TO_POPULATE])) {
            return $this->itemNormalizer->denormalize($data, $type, $format, $context);
        }

        $class = $context['input']['class'];
        $constructor = (new \ReflectionClass($class))->getConstructor();

        if (null !== $constructor) {
            $this->assertConstructorArgumentsPresence($constructor, $class, $data);
        }

        return $this->itemNormalizer->denormalize($data, $type, $format, $context);
    }

    private function assertConstructorArgumentsPresence(
        \ReflectionMethod $constructor,
        string $class,
        mixed $data,
    ): void {
        $parameters = $constructor->getParameters();

        $missingFields = [];
        foreach ($parameters as $parameter) {
            $name = $this->nameConverter->normalize($parameter->getName(), $class);
            if (!isset($data[$name]) && !($parameter->allowsNull() || $parameter->isDefaultValueAvailable())) {
                $missingFields[] = $name;
            }
        }

        if (count($missingFields) > 0) {
            throw new MissingConstructorArgumentsException(
                sprintf('Request does not have the following required fields specified: %s.', implode(', ', $missingFields)),
            );
        }
    }
}
