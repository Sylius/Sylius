<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Provider\Resource;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
    public function getResources()
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
    public function getResourcesLocales()
    {
        $resourcesLocales = [];

        foreach ($this->resourceProviders as $resourceProvider) {
            $resourcesLocales = array_merge($resourcesLocales, $resourceProvider->getResourcesLocales());
        }

        return array_values(array_unique($resourcesLocales));
    }
}
