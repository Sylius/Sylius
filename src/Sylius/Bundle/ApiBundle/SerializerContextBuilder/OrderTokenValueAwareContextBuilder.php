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
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderTokenValueAwareContextBuilder extends AbstractInputContextBuilder
{
    protected function supportsClass(string $class): bool
    {
        return is_a($class, OrderTokenValueAwareInterface::class, true);
    }

    protected function supports(Request $request, array $context, ?array $extractedAttributes): bool
    {
        return null !== $this->resolveOrderTokenValueFromUriVariables($context, $extractedAttributes);
    }

    protected function resolveValue(array $context, ?array $extractedAttributes): mixed
    {
        return $this->resolveOrderTokenValueFromUriVariables($context, $extractedAttributes);
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
}
