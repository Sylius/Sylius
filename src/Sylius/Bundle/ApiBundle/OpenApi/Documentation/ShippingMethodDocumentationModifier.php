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

final class ShippingMethodDocumentationModifier implements DocumentationModifierInterface
{
    public const ROUTE_ADMIN_SHIPPING_METHODS = '/admin/shipping-methods';

    public const ROUTE_ADMIN_SHIPPING_METHOD = '/admin/shipping-methods/{code}';

    /**
     * @param string[] $ruleTypes
     * @param string[] $shippingMethodCalculators
     */
    public function __construct(
        private string $apiRoute,
        private array $ruleTypes,
        private array $shippingMethodCalculators,
    ) {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        if (isset($schemas['ShippingMethod.jsonld-shop.shipping_method.read'])) {
            $schemas['ShippingMethod.jsonld-shop.shipping_method.read']['properties']['price'] = [
                'type' => 'integer',
                'readOnly' => true,
                'default' => 0,
            ];

            $components = $components->withSchemas($schemas);
            $docs = $docs->withComponents($components);
        }

        return $this->modifyDescription($docs);
    }

    private function modifyDescription(OpenApi $docs): OpenApi
    {
        $paths = $docs->getPaths();

        $this->addDescription($paths, sprintf('%s%s', $this->apiRoute, self::ROUTE_ADMIN_SHIPPING_METHODS), 'Post');
        $this->addDescription($paths, sprintf('%s%s', $this->apiRoute, self::ROUTE_ADMIN_SHIPPING_METHOD), 'Put');

        return $docs->withPaths($paths);
    }

    private function addDescription(Paths $paths, string $path, string $method): void
    {
        $pathItem = $paths->getPath($path);
        $methodGet = sprintf('get%s', $method);
        $operation = $pathItem?->$methodGet();
        if (null === $operation) {
            return;
        }

        $description = sprintf(
            "%s\n\n Allowed rule types: `%s` \n\n Allowed calculators: `%s`",
            $operation->getDescription(),
            implode('`, `', array_keys($this->ruleTypes)),
            implode('`, `', array_keys($this->shippingMethodCalculators)),
        );

        $operation = $operation->withDescription($description);
        $methodWith = sprintf('with%s', $method);
        $pathItem = $pathItem->$methodWith($operation);
        $paths->addPath($path, $pathItem);
    }
}
