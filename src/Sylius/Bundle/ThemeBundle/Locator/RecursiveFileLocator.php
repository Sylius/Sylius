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

namespace Sylius\Bundle\ThemeBundle\Locator;

use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Symfony\Component\Finder\SplFileInfo;

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
     * @var int
     */
    private $depth;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param array|string[] $paths An array of paths where to look for resources
     * @param int|null $depth Restrict depth to search for configuration file inside theme folder
     */
    public function __construct(FinderFactoryInterface $finderFactory, array $paths, ?int $depth = null)
    {
        $this->finderFactory = $finderFactory;
        $this->paths = $paths;
        $this->depth = $depth;
    }

    /**
     * {@inheritdoc}
     */
    public function locateFileNamed(string $name): string
    {
        return $this->doLocateFilesNamed($name)->current();
    }

    /**
     * {@inheritdoc}
     */
    public function locateFilesNamed(string $name): array
    {
        return iterator_to_array($this->doLocateFilesNamed($name));
    }

    /**
     * @param string $name
     *
     * @return \Generator
     */
    private function doLocateFilesNamed(string $name): \Generator
    {
        $this->assertNameIsNotEmpty($name);

        $found = false;
        foreach ($this->paths as $path) {
            try {
                $finder = $this->finderFactory->create();

                if ($this->depth !== null) {
                    $finder->depth(sprintf('<= %d', $this->depth));
                }

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
    private function assertNameIsNotEmpty(string $name): void
    {
        if (null === $name || '' === $name) {
            throw new \InvalidArgumentException(
                'An empty file name is not valid to be located.'
            );
        }
    }
}
