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

use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    PathHiderDocumentationModifier::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class PathHiderDocumentationModifier implements DocumentationModifierInterface
{
    /**
     * @param string[] $apiRoutes
     */
    public function __construct(private array $apiRoutes)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        /** @var array<string, PathItem> $pathItems */
        $pathItems = $docs->getPaths()->getPaths();

        foreach ($this->apiRoutes as $apiRoute) {
            if (array_key_exists($apiRoute, $pathItems)) {
                unset($pathItems[$apiRoute]);
            }
        }

        $paths = new Paths();

        foreach ($pathItems as $path => $pathItem) {
            $paths->addPath($path, $pathItem);
        }

        return $docs->withPaths($paths);
    }
}
