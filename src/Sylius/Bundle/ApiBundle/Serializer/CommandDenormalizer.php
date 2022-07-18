<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @experimental
 */
final class CommandDenormalizer implements ContextAwareDenormalizerInterface
{
    private const OBJECT_TO_POPULATE = 'object_to_populate';

    public function __construct(private DenormalizerInterface $itemNormalizer)
    {
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

        $constructor = (new \ReflectionClass($context['input']['class']))->getConstructor();

        if (null !== $constructor) {
            $this->assertConstructorArgumentsPresence($constructor, $data);
        }

        return $this->itemNormalizer->denormalize($data, $type, $format, $context);
    }

    private function assertConstructorArgumentsPresence(\ReflectionMethod $constructor, $data): void
    {
        $parameters = $constructor->getParameters();

        $missingFields = [];
        foreach ($parameters as $parameter) {
            if (!isset($data[$parameter->getName()]) && !($parameter->allowsNull() || $parameter->isDefaultValueAvailable())) {
                $missingFields[] = $parameter->getName();
            }
        }

        if (count($missingFields) > 0) {
            throw new MissingConstructorArgumentsException(
                sprintf('Request does not have the following required fields specified: %s.', implode(', ', $missingFields)),
            );
        }
    }
}
