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

use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResource;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslatorResourceProvider implements TranslatorResourceProviderInterface
{
    /**
     * @var TranslationResourceInterface[]
     */
    private $resources = [];

    /**
     * @var array
     */
    private $resourcesLocales = [];

    /**
     * @var array
     */
    private $filepaths;

    /**
     * @param array $filepaths
     */
    public function __construct(array $filepaths = [])
    {
        $this->filepaths = $filepaths;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        $this->initializeIfNeeded();

        return $this->resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesLocales()
    {
        $this->initializeIfNeeded();

        return $this->resourcesLocales;
    }

    private function initializeIfNeeded()
    {
        foreach ($this->filepaths as $key => $filepath) {
            $resource = new TranslationResource($filepath);

            $this->resources[] = $resource;
            $this->resourcesLocales[] = $resource->getLocale();
        }

        $this->resourcesLocales = array_unique($this->resourcesLocales);
        $this->filepaths = [];
    }
}
