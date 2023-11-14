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

use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;

/** @experimental */
final class PromotionDocumentationModifier implements DocumentationModifierInterface
{
    /**
     * @param string[] $actionTypes
     * @param string[] $ruleTypes
     */
    public function __construct(
        private string $apiRoute,
        private array $actionTypes,
        private array $ruleTypes,
    ) {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $paths = $docs->getPaths();

        $this->addDescription($paths, sprintf('%s/admin/promotions', $this->apiRoute), 'Post');
        $this->addDescription($paths, sprintf('%s/admin/promotions/{code}', $this->apiRoute), 'Put');

        return $docs->withPaths($paths);
    }


    public function addDescription(Paths $paths, string $path, string $method): void
    {
        $pathItem = $paths->getPath($path);
        $methodGet = sprintf('get%s', $method);
        $operation = $pathItem->$methodGet();

        $description = sprintf(
            "%s\n\n Allowed rule types: `%s` \n\n Allowed action types: `%s`",
            $operation->getDescription(),
            implode('`, `', array_keys($this->ruleTypes)),
            implode('`, `', array_keys($this->actionTypes))
        );

        $operation = $operation->withDescription($description);
        $methodWith = sprintf('with%s', $method);
        $pathItem = $pathItem->$methodWith($operation);
        $paths->addPath($path, $pathItem);
    }
}
