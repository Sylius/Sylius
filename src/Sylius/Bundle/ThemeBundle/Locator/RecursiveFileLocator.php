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

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RecursiveFileLocator implements FileLocatorInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array
     */
    private $paths;

    /**
     * @param Filesystem $filesystem
     * @param string|array $paths A path or an array of paths where to look for resources
     */
    public function __construct(Filesystem $filesystem, $paths = [])
    {
        $this->filesystem = $filesystem;
        $this->paths = (array) $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('An empty file name is not valid to be located.');
        }

        if ($this->filesystem->isAbsolutePath($name)) {
            if (!$this->filesystem->exists($name)) {
                throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $name));
            }

            return $name;
        }

        $directories = $this->paths;
        if (null !== $currentPath) {
            $directories[] = $currentPath;

            $directories = array_values(array_unique($directories));
        }

        $filepaths = [];

        $finder = new Finder();
        $finder
            ->files()
            ->name($name)
            ->ignoreUnreadableDirs()
            ->in($directories)
        ;

        /** @var SplFileInfo $file */
        if ($first && null !== $file = $finder->getIterator()->current()) {
            return $file->getPathname();
        }

        foreach ($finder as $file) {
            $filepaths[] = $file->getPathname();
        }

        if (!$filepaths) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist (in: %s).', $name, implode(', ', $directories)));
        }

        return array_values(array_unique($filepaths));
    }
}
