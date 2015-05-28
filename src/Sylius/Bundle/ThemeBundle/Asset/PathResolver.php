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
        $dotIndex = strpos($path, '.');

        return substr($path, 0, $dotIndex) . '_' . $theme->getHashCode() . substr($path, $dotIndex);
    }
}