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
use Symfony\Component\HttpFoundation\Request;

final class UriVariablesAwareContextBuilder extends AbstractInputContextBuilder
{
    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        string $attributeClass,
        string $defaultConstructorArgumentName,
        private readonly string $commandInterface,
        private readonly string $objectInterface,
    ) {
        parent::__construct($decoratedContextBuilder, $attributeClass, $defaultConstructorArgumentName);
    }

    protected function supportsClass(string $class): bool
    {
        return is_a($class, $this->commandInterface, true);
    }

    protected function supports(Request $request, array $context, ?array $extractedAttributes): bool
    {
        return null !== $this->resolveValueFromUriVariables($context, $extractedAttributes);
    }

    protected function resolveValue(array $context, ?array $extractedAttributes): mixed
    {
        return $this->resolveValueFromUriVariables($context, $extractedAttributes);
    }

    private function resolveValueFromUriVariables(array $context, ?array $attributes): ?string
    {
        if (
            null !== $attributes &&
            isset($attributes['operation']) &&
            $attributes['operation'] instanceof HttpOperation
        ) {
            $operation = $attributes['operation'];
            foreach ($operation->getUriVariables() as $uriVariable) {
                if (false === is_a($uriVariable->getFromClass(), $this->objectInterface, true)) {
                    continue;
                }

                $identifier = $uriVariable->getParameterName() ?? $this->defaultConstructorArgumentName;

                return $context['uri_variables'][$identifier] ?? null;
            }
        }

        return null;
    }
}
