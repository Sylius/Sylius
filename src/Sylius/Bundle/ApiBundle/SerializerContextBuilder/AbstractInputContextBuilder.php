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

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

abstract class AbstractInputContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        protected readonly SerializerContextBuilderInterface $decoratedContextBuilder,
        protected readonly string $attributeClass,
        protected readonly string $defaultConstructorArgumentName,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);
        $inputClass = $this->getInputClassFromContext($context);

        if ($inputClass === null || !$this->supportsClass($inputClass) || !$this->supports($request, $context, $extractedAttributes)) {
            return $context;
        }

        $constructorArgumentName = $this->getConstructorArgumentName($inputClass) ?? $this->defaultConstructorArgumentName;

        if (isset($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass]) && is_array($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass])) {
            $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = array_merge($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass], [$constructorArgumentName => $this->resolveValue($context, $extractedAttributes)]);
        } else {
            $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = [$constructorArgumentName => $this->resolveValue($context, $extractedAttributes)];
        }

        return $context;
    }

    abstract protected function supportsClass(string $class): bool;

    abstract protected function supports(Request $request, array $context, ?array $extractedAttributes): bool;

    abstract protected function resolveValue(array $context, ?array $extractedAttributes): mixed;

    /**
     * @param array<string, mixed> $context
     */
    private function getInputClassFromContext(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }

    private function getConstructorArgumentName(string $class): ?string
    {
        $classReflection = new \ReflectionClass($class);
        $attributes = $classReflection->getAttributes($this->attributeClass);

        if (count($attributes) === 0) {
            return null;
        }

        $channelCodeAwareAttribute = $attributes[0]->newInstance();

        return $channelCodeAwareAttribute->constructorArgumentName;
    }
}
