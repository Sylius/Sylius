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

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Api\IriConverterInterface as LegacyIriConverterInterface;
use ApiPlatform\Metadata\Operation;

/**
 * Wrapper between the legacy iri converter and the new one
 *
 * @experimental
 */
final class IriConverter implements LegacyIriConverterInterface, IriConverterInterface
{
    public function __construct(
        private LegacyIriConverterInterface $legacyIriConverter,
        private IriConverterInterface $iriConverter,
    ) {
    }

    public function getItemFromIri(string $iri, array $context = [])
    {
        return $this->legacyIriConverter->getItemFromIri($iri, $context);
    }

    public function getIriFromItem($item, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->legacyIriConverter->getIriFromItem($item, $referenceType);
    }

    public function getIriFromResourceClass(
        string $resourceClass,
        int $referenceType = UrlGeneratorInterface::ABS_PATH,
    ): string {
        return $this->legacyIriConverter->getIriFromResourceClass($resourceClass, $referenceType);
    }

    public function getItemIriFromResourceClass(
        string $resourceClass,
        array $identifiers,
        int $referenceType = UrlGeneratorInterface::ABS_PATH,
    ): string {
        return $this->legacyIriConverter->getItemIriFromResourceClass($resourceClass, $identifiers, $referenceType);
    }

    public function getSubresourceIriFromResourceClass(
        string $resourceClass,
        array $identifiers,
        int $referenceType = UrlGeneratorInterface::ABS_PATH,
    ): string {
        return $this->legacyIriConverter->getSubresourceIriFromResourceClass(
            $resourceClass,
            $identifiers,
            $referenceType,
        );
    }

    public function getResourceFromIri(string $iri, array $context = [], ?Operation $operation = null): object
    {
        return $this->iriConverter->getResourceFromIri($iri, $context, $operation);
    }

    public function getIriFromResource(
        $resource,
        int $referenceType = UrlGeneratorInterface::ABS_PATH,
        ?Operation $operation = null,
        array $context = [],
    ): ?string {
        return $this->iriConverter->getIriFromResource($resource, $referenceType, $operation, $context);
    }
}
