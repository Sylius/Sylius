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

use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RecursiveFileLocator implements FileLocatorInterface
{
    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * @var array
     */
    private $paths;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param array $paths An array of paths where to look for resources
     */
    public function __construct(FinderFactoryInterface $finderFactory, array $paths)
    {
        $this->finderFactory = $finderFactory;
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function locateFileNamed($name)
    {
        return $this->doLocateFilesNamed($name)->current();
    }

    /**
     * {@inheritdoc}
     */
    public function locateFilesNamed($name)
    {
        return iterator_to_array($this->doLocateFilesNamed($name));
    }

    /**
     * @param string $name
     *
     * @return \Generator
     */
    private function doLocateFilesNamed($name)
    {
        $this->assertNameIsNotEmpty($name);

        $found = false;
        foreach ($this->paths as $path) {
            try {
                $finder = $this->finderFactory->create();
                $finder
                    ->files()
                    ->name($name)
                    ->ignoreUnreadableDirs()
                    ->in($path);

                /** @var SplFileInfo $file */
                foreach ($finder as $file) {
                    $found = true;

                    yield $file->getPathname();
                }
            } catch (\InvalidArgumentException $exception) {
            }
        }

        if (false === $found) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" does not exist (searched in the following directories: %s).',
                $name,
                implode(', ', $this->paths)
            ));
        }
    }

    /**
     * @param string $name
     */
    private function assertNameIsNotEmpty($name)
    {
        if (null === $name || '' === $name) {
            throw new \InvalidArgumentException(
                'An empty file name is not valid to be located.'
            );
        }
    }
}
