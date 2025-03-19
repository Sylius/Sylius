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

final readonly class NotPrefixedRoutesRemovalDocumentationModifier implements DocumentationModifierInterface
{
    /** @param array<string> $prefixesToRemove */
    public function __construct(private array $prefixesToRemove)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        /** @var array<string, PathItem> $pathItems */
        $pathItems = $docs->getPaths()->getPaths();

        foreach ($pathItems as $path => $pathItem) {
            foreach ($this->prefixesToRemove as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    continue 2;
                }
            }

            unset($pathItems[$path]);
        }

        $paths = new Paths();
        foreach ($pathItems as $path => $pathItem) {
            $paths->addPath($path, $pathItem);
        }

        return $docs->withPaths($paths);
    }
}
