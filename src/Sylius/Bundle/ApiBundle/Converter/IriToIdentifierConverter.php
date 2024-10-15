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

namespace Sylius\Bundle\ApiBundle\Converter;

use ApiPlatform\Api\UriVariablesConverterInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Exception\InvalidIdentifierException;
use ApiPlatform\Metadata\Exception\RuntimeException;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\UriVariablesResolverTrait;
use Sylius\Bundle\ApiBundle\Exception\NoRouteMatchesException;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingExceptionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Logic of this class is based on {@see \ApiPlatform\Symfony\Routing\IriConverter}
 * This class provides the identifier from path without retrieving the database object
 *
 * @internal
 */
final class IriToIdentifierConverter implements IriToIdentifierConverterInterface
{
    use UriVariablesResolverTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        UriVariablesConverterInterface $uriVariablesConverter,
    ) {
        $this->uriVariablesConverter = $uriVariablesConverter;
    }

    public function getIdentifier(?string $iri, ?Operation $operation = null): ?string
    {
        if ($iri === null || $iri === '') {
            return null;
        }

        try {
            $parameters = $this->router->match($iri);
        } catch (RoutingExceptionInterface $e) {
            throw new NoRouteMatchesException(sprintf('No route matches "%s".', $iri), $e->getCode(), $e);
        }

        if (!isset($parameters['_api_resource_class'], $parameters['_api_operation_name'])) {
            throw new InvalidArgumentException(sprintf('No resource associated to "%s".', $iri));
        }

        if ($operation && !is_a($parameters['_api_resource_class'], $operation->getClass(), true)) {
            throw new InvalidArgumentException(sprintf('The iri "%s" does not reference the correct resource.', $iri));
        }

        $operation = $operation ?? $this->createOperation($parameters);

        if ($operation instanceof CollectionOperationInterface) {
            throw new InvalidArgumentException(sprintf('The iri "%s" references a collection not an item.', $iri));
        }

        if (!$operation instanceof HttpOperation) {
            throw new RuntimeException(sprintf('The iri "%s" does not reference an HTTP operation.', $iri));
        }

        try {
            $identifiers = $this->getOperationUriVariables($operation, $parameters, $parameters['_api_resource_class']);
        } catch (InvalidIdentifierException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        if (count($identifiers) > 1) {
            throw new InvalidArgumentException(sprintf('%s does not support subresources', self::class));
        }

        return (string) array_values($identifiers)[0];
    }

    public function isIdentifier($fieldValue): bool
    {
        if (!is_string($fieldValue)) {
            return false;
        }

        try {
            $parameters = $this->router->match($fieldValue);
        } catch (RoutingExceptionInterface) {
            return false;
        }

        return isset($parameters['_api_resource_class']);
    }

    private function createOperation(array $parameters): Operation
    {
        return $this->resourceMetadataCollectionFactory->create($parameters['_api_resource_class'])->getOperation($parameters['_api_operation_name']);
    }
}
