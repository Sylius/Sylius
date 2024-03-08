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

use ApiPlatform\Core\DataProvider\OperationDataProviderTrait;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\InvalidIdentifierException;
use ApiPlatform\Core\Identifier\IdentifierConverterInterface;
use ApiPlatform\Core\Util\AttributesExtractor;
use Sylius\Bundle\ApiBundle\Exception\NoRouteMatchesException;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingExceptionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Logic of this class is based on ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter, This class provide `id` from path but it doesn't fetch object from database
 */
final class IriToIdentifierConverter implements IriToIdentifierConverterInterface
{
    use OperationDataProviderTrait;

    public function __construct(
        private RouterInterface $router,
        IdentifierConverterInterface $identifierConverter,
    ) {
        $this->identifierConverter = $identifierConverter;
    }

    public function getIdentifier(?string $iri): ?string
    {
        if ($iri === null || $iri === '') {
            return null;
        }

        try {
            $parameters = $this->router->match($iri);
        } catch (RoutingExceptionInterface $e) {
            throw new NoRouteMatchesException(sprintf('No route matches "%s".', $iri), (int) $e->getCode(), $e);
        }

        if (!isset($parameters['_api_resource_class'])) {
            throw new InvalidArgumentException(sprintf('No resource associated to "%s".', $iri));
        }

        $attributes = AttributesExtractor::extractAttributes($parameters);

        try {
            $identifiers = $this->extractIdentifiers($parameters, $attributes);
        } catch (InvalidIdentifierException $e) {
            throw new InvalidArgumentException($e->getMessage(), (int) $e->getCode(), $e);
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
}
