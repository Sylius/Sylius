<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Attribute\ShipmentIdAware;
use Sylius\Bundle\ApiBundle\Command\ShipmentIdAwareInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final readonly class ShipmentAwareContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decoratedContextBuilder,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);
        $inputClass = $this->getInputClassFromContext($context);

        if ($inputClass === null || !is_a($inputClass, ShipmentIdAwareInterface::class, true)) {
            return $context;
        }

        $constructorArgumentName = $this->getConstructorArgumentName($inputClass) ?? 'shipmentId';
        $shipmentId = $this->resolveShipmentIdFromUriVariables($context, $extractedAttributes);

        if (null !== $shipmentId) {
            if (isset($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass]) && is_array($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass])) {
                $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = array_merge($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass], [$constructorArgumentName => $shipmentId]);
            } else {
                $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = [$constructorArgumentName => $shipmentId];
            }
        }

        return $context;
    }

    private function getConstructorArgumentName(string $class): ?string
    {
        $classReflection = new \ReflectionClass($class);
        $attributes = $classReflection->getAttributes(ShipmentIdAware::class);

        if (count($attributes) === 0) {
            return null;
        }

        /** @var ShipmentIdAware $shipmentIdAware */
        $shipmentIdAware = $attributes[0]->newInstance();

        return $shipmentIdAware->constructorArgumentName;
    }

    private function resolveShipmentIdFromUriVariables(array $context, ?array $attributes): ?string
    {
        if (
            null !== $attributes &&
            isset($attributes['operation']) &&
            $attributes['operation'] instanceof HttpOperation
        ) {
            $operation = $attributes['operation'];
            foreach ($operation->getUriVariables() as $uriVariable) {
                if (false === is_a($uriVariable->getFromClass(), ShipmentInterface::class, true)) {
                    continue;
                }

                $identifier = $uriVariable->getFromProperty() ?? $uriVariable->getParameterName() ?? 'id';

                return $context['uri_variables'][$identifier] ?? null;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function getInputClassFromContext(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }
}
