<?php

namespace Sylius\Bundle\ThemeBundle\Asset;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PathResolver implements PathResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($path, ThemeInterface $theme)
    {
        return str_replace('bundles/', 'bundles/_theme/' . $theme->getLogicalName() . '/', $path);
    }
}