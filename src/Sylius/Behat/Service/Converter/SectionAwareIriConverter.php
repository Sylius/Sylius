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

namespace Sylius\Behat\Service\Converter;

use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Api\IdentifiersExtractor;
use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Identifier\CompositeIdentifierParser;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Util\ResourceClassInfoTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingExceptionInterface;
use Symfony\Component\Routing\RouterInterface;

/** @experimental */
final class SectionAwareIriConverter implements SectionAwareIriConverterInterface
{
    use ResourceClassInfoTrait;

    private IdentifiersExtractorInterface $identifiersExtractor;

    public function __construct(
        private RouteNameResolverInterface $routeNameResolver,
        private RouterInterface $router,
        PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        PropertyMetadataFactoryInterface $propertyMetadataFactory,
        ?PropertyAccessorInterface $propertyAccessor = null,
        ?IdentifiersExtractorInterface $identifiersExtractor = null,
        ?ResourceClassResolverInterface $resourceClassResolver = null,
        ?ResourceMetadataFactoryInterface $resourceMetadataFactory = null,
    ) {
        $this->identifiersExtractor = $identifiersExtractor ?: new IdentifiersExtractor($propertyNameCollectionFactory, $propertyMetadataFactory, $propertyAccessor ?? PropertyAccess::createPropertyAccessor());
        $this->resourceClassResolver = $resourceClassResolver;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function getIriFromResourceInSection(object $item, string $section, ?int $referenceType = null): string
    {
        $resourceClass = $this->getResourceClass($item, true);

        try {
            $identifiers = $this->identifiersExtractor->getIdentifiersFromItem($item);
        } catch (RuntimeException $e) {
            throw new InvalidArgumentException(sprintf('Unable to generate an IRI for the item of type "%s"', $resourceClass), $e->getCode(), $e);
        }

        $routeName = $this->routeNameResolver->getRouteName($resourceClass, OperationType::ITEM, $section ? ['section' => $section] : []);
        $metadata = $this->resourceMetadataFactory->create($resourceClass);

        if (\count($identifiers) > 1 && true === $metadata->getAttribute('composite_identifier', true)) {
            $identifiers = ['id' => CompositeIdentifierParser::stringify($identifiers)];
        }

        try {
            return $this->router->generate($routeName, $identifiers, $this->getReferenceType($resourceClass, $referenceType));
        } catch (RoutingExceptionInterface $e) {
            throw new InvalidArgumentException(sprintf('Unable to generate an IRI for "%s".', $resourceClass), $e->getCode(), $e);
        }
    }

    private function getReferenceType(string $resourceClass, ?int $referenceType): ?int
    {
        if (null === $referenceType && null !== $this->resourceMetadataFactory) {
            $metadata = $this->resourceMetadataFactory->create($resourceClass);
            $referenceType = $metadata->getAttribute('url_generation_strategy');
        }

        return $referenceType ?? UrlGeneratorInterface::ABS_PATH;
    }
}
