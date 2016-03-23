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
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
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

    private $themeContext;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param array                  $paths         An array of paths where to look for resources
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
     * Sets the Theme Context.
     *
     * @param ThemeContextInterface|null $themeContext Theme context
     */
    public function setThemeContext(ThemeContextInterface $themeContext = null)
    {
        $this->themeContext = $themeContext;
    }

    /**
     * @param string $name
     *
     * @return \Generator
     */
    private function doLocateFilesNamed($name)
    {
        $this->assertNameIsNotEmpty($name);
        $this->applySuffixForPaths();
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

    private function applySuffixForPaths()
    {
        if (!isset($this->themeContext) || null === $this->themeContext->getName()) {
            return;
        }

        $paths = [];
        foreach ($this->paths as $path) {
            $paths[] = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->themeContext->getName();
        }

        $this->paths = $paths;
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
