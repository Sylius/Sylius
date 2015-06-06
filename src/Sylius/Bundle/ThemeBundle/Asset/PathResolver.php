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

        $dotIndex = strpos($basename, '.');
        $basename = substr($basename, 0, $dotIndex) . '_' . $theme->getHashCode() . substr($basename, $dotIndex);

        return $dirname . DIRECTORY_SEPARATOR . $basename;
    }
}