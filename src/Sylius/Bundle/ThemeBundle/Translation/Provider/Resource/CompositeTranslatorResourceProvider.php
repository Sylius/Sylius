<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Translation\Provider\Resource;

final class CompositeTranslatorResourceProvider implements TranslatorResourceProviderInterface
{
    /**
     * @var array|TranslatorResourceProviderInterface[]
     */
    private $resourceProviders;

    /**
     * @param TranslatorResourceProviderInterface[] $resourceProviders
     */
    public function __construct(array $resourceProviders = [])
    {
        $this->resourceProviders = $resourceProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources(): array
    {
        $resources = [];

        foreach ($this->resourceProviders as $resourceProvider) {
            $resources = array_merge($resources, $resourceProvider->getResources());
        }

        return $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesLocales(): array
    {
        $resourcesLocales = [];

        foreach ($this->resourceProviders as $resourceProvider) {
            $resourcesLocales = array_merge($resourcesLocales, $resourceProvider->getResourcesLocales());
        }

        return array_values(array_unique($resourcesLocales));
    }
}
