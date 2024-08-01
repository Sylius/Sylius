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

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final readonly class OrderTokenValueAwareContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decoratedContextBuilder,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);
        $inputClass = $this->getInputClassFromContext($context);

        if ($inputClass === null || !is_a($inputClass, OrderTokenValueAwareInterface::class, true)) {
            return $context;
        }

        $constructorArgumentName = $this->getConstructorArgumentName($inputClass) ?? 'orderTokenValue';
        $orderTokenValue = $this->resolveOrderTokenValueFromUriVariables($context, $extractedAttributes);

        if (null !== $orderTokenValue) {
            if (isset($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass]) && is_array($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass])) {
                $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = array_merge($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass], [$constructorArgumentName => $orderTokenValue]);
            } else {
                $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = [$constructorArgumentName => $orderTokenValue];
            }
        }

        return $context;
    }

    private function getConstructorArgumentName(string $class): ?string
    {
        $classReflection = new \ReflectionClass($class);
        $attributes = $classReflection->getAttributes(OrderTokenValueAware::class);

        if (count($attributes) === 0) {
            return null;
        }

        /** @var OrderTokenValueAware $orderTokenValueAware */
        $orderTokenValueAware = $attributes[0]->newInstance();

        return $orderTokenValueAware->constructorArgumentName;
    }

    private function resolveOrderTokenValueFromUriVariables(array $context, ?array $attributes): ?string
    {
        if (
            null !== $attributes &&
            isset($attributes['operation']) &&
            $attributes['operation'] instanceof HttpOperation
        ) {
            $operation = $attributes['operation'];
            foreach ($operation->getUriVariables() as $uriVariable) {
                if (false === is_a($uriVariable->getFromClass(), OrderInterface::class, true)) {
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
