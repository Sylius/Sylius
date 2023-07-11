<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\OpenApi\Documentation;

use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;

/** @experimental */
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
