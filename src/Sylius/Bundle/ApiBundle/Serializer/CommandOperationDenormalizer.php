<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandOperationDenormalizer implements ContextAwareDenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $objectNormalizer;

    public function __construct(DenormalizerInterface $objectNormalizer)
    {
        $this->objectNormalizer = $objectNormalizer;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return isset($context['input']['class']);
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $parameters = (new \ReflectionClass($context['input']['class']))->getConstructor()->getParameters();

        $missingFields = [];
        foreach ($parameters as $parameter) {
            if (!isset($data[$parameter->getName()]) && !$parameter->allowsNull()) {
                $missingFields[] = $parameter->getName();
            }
        }

        if (count($missingFields) === 0) {
            return $this->objectNormalizer->denormalize($data, $this->getInputClassName($context), $format, $context);
        }

        throw new MissingConstructorArgumentsException(
            sprintf('Request does not have the following required fields specified: %s.', implode(', ', $missingFields))
        );
    }

    private function getInputClassName(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }
}
