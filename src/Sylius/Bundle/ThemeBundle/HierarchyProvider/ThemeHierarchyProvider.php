<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\HierarchyProvider;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeHierarchyProvider implements ThemeHierarchyProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getThemeHierarchy(ThemeInterface $theme = null)
    {
        if (null === $theme) {
            return [];
        }

        $parents = [];
        foreach ($theme->getParents() as $parent) {
            $parents = array_merge(
                $parents,
                $this->getThemeHierarchy($parent)
            );
        }

        return array_merge([$theme], $parents);
    }
}
