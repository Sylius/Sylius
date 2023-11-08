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

/** @experimental */
final class TranslatableLocaleKeyDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $actions = array_filter($schemas->getArrayCopy(), function ($key) {
            return preg_match('/\.(create|update)$/', $key);
        }, \ARRAY_FILTER_USE_KEY);

        foreach ($actions as $key => $action) {
            if (null === $properties = $action['properties'] ?? null) {
                continue;
            }

            if (null === $translations = $properties['translations'] ?? null) {
                continue;
            }

            if (null === $example = $translations['example'] ?? null) {
                continue;
            }

            $schemas[$key]['properties']['translations']['example'] = array_values($example);
        }

        return $docs->withComponents(
            $components->withSchemas($schemas),
        );
    }
}
