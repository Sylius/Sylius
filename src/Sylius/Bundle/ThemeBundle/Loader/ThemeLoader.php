<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Loader;

use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Filesystem\Filesystem;
use Sylius\Bundle\ThemeBundle\Filesystem\FilesystemInterface;
use Symfony\Component\Config\Loader\Loader;

/**
 * Abstract loader for themes based on files.
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class ThemeLoader extends Loader
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var ThemeFactoryInterface
     */
    private $themeFactory;

    /**
     * @param FilesystemInterface $filesystem
     * @param ThemeFactoryInterface $themeFactory
     */
    public function __construct(FilesystemInterface $filesystem, ThemeFactoryInterface $themeFactory)
    {
        $this->filesystem = $filesystem;
        $this->themeFactory = $themeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (!$this->filesystem->exists($resource)) {
            throw new \InvalidArgumentException(sprintf('Given theme metadata file "%s" does not exists!', $resource));
        }

        $themeData = $this->transformResourceContentsToArray($this->filesystem->getFileContents($resource));

        $theme = $this->themeFactory->createFromArray($themeData);
        $theme->setPath(substr($resource, 0, strrpos($resource, '/')));

        return $theme;
    }

    /**
     * Returns theme data array from resource contents.
     *
     * @param string $contents
     *
     * @return array
     */
    abstract protected function transformResourceContentsToArray($contents);
}
