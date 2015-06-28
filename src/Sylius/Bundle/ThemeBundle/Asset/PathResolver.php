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
        $dirname = dirname($path);
        $basename = basename($path);

        return $dirname . '-' . $theme->getHashCode() . DIRECTORY_SEPARATOR . $basename;
    }
}