<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use Sylius\Bundle\ThemeBundle\Filesystem\FilesystemInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class JsonFileConfigurationLoader implements ConfigurationLoaderInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $this->assertFileExists($path);

        $contents = $this->filesystem->getFileContents($path);

        return array_merge(
            ['path' => dirname($path)],
            json_decode($contents, true)
        );
    }

    /**
     * @param string $path
     */
    private function assertFileExists($path)
    {
        if (!$this->filesystem->exists($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Given file "%s" does not exist!',
                $path
            ));
        }
    }
}
