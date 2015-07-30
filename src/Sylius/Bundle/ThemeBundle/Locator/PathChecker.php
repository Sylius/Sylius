<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Locator;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PathChecker implements PathCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function processPaths(array $paths, array $parameters, array $themes = [])
    {
        foreach ($paths as $path) {
            $path = $this->processPath($path, $parameters, $themes);

            if (null !== $path) {
                return $path;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function processPath($path, array $parameters, array $themes = [])
    {
        if (false === strpos($path, '%theme_path%')) {
            $checkedPath = $this->buildPath($path, $parameters);

            return $this->pathExists($checkedPath) ? $checkedPath : null;
        }

        /** @var ThemeInterface[] $themes */
        foreach ($themes as $theme) {
            $checkedPath = $this->buildPath(
                $path,
                array_merge($parameters, ['%theme_path%' => $theme->getPath()])
            );

            if ($this->pathExists($checkedPath)) {
                return $checkedPath;
            }
        }

        return null;
    }

    /**
     * @param string $path
     * @param array $parameters
     *
     * @return string
     */
    private function buildPath($path, array $parameters = [])
    {
        return strtr($path, $parameters);
    }

    /**
     * @param string $path
     *
     * @return boolean
     */
    private function pathExists($path)
    {
        return file_exists($path);
    }
}