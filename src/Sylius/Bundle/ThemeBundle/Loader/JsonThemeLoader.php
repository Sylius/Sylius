<?php

namespace Sylius\Bundle\ThemeBundle\Loader;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class JsonThemeLoader extends ThemeLoader
{
    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'json' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function transformResourceContentsToArray($contents)
    {
        return json_decode($contents, true);
    }
}