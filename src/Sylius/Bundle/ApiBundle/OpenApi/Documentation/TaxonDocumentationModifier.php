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

namespace Sylius\Bundle\ApiBundle\OpenApi\Documentation;

use ApiPlatform\OpenApi\OpenApi;

final class TaxonDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(private string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $path = sprintf('%s/shop/taxons', $this->apiRoute);

        $paths = $docs->getPaths();
        $pathItem = $paths->getPath($path);
        $operation = $pathItem?->getGet();
        if (null === $operation) {
            return $docs;
        }

        $operation = $operation
            ->withSummary('Retrieves the collection of enabled Taxon resources for the current channel.')
            ->withDescription(
                'Returns the direct enabled children of the Menu Taxon configured for the current channel. ' .
                "The Menu Taxon is resolved from the active channel configuration. " .
                "If no Menu Taxon is configured for the channel, the taxon with code 'category' is used as the default root.",
            )
        ;

        $pathItem = $pathItem->withGet($operation);
        $paths->addPath($path, $pathItem);

        return $docs->withPaths($paths);
    }
}
