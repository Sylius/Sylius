<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Provider;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslatorResourceProvider implements TranslatorResourceProviderInterface
{
    /**
     * @var TranslationResourceInterface[]
     */
    private $resources;

    /**
     * @var array
     */
    private $resourcesLocales = [];

    /**
     * @param array $filepaths
     */
    public function __construct(array $filepaths = [])
    {
        foreach ($filepaths as $filepath) {
            $resource = new TranslationResource($filepath);

            $this->resources[] = $resource;
            $this->resourcesLocales[] = $resource->getLocale();
        }

        $this->resourcesLocales = array_unique($this->resourcesLocales);
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesLocales()
    {
        return $this->resourcesLocales;
    }
}
