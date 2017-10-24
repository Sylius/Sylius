<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Loader;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

final class CircularDependencyChecker implements CircularDependencyCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function check(ThemeInterface $theme, array $previousThemes = []): void
    {
        if (0 === count($theme->getParents())) {
            return;
        }

        $previousThemes = array_merge($previousThemes, [$theme]);
        foreach ($theme->getParents() as $parent) {
            if (in_array($parent, $previousThemes, true)) {
                throw new CircularDependencyFoundException(array_merge($previousThemes, [$parent]));
            }

            $this->check($parent, $previousThemes);
        }
    }
}
