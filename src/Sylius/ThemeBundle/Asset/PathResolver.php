<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ThemeBundle\Asset;

use Sylius\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PathResolver implements PathResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($path, ThemeInterface $theme)
    {
        return str_replace('bundles/', 'bundles/_themes/' . $theme->getName() . '/', $path);
    }
}
