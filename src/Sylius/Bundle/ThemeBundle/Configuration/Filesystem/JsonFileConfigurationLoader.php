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

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use Sylius\Bundle\ThemeBundle\Filesystem\FilesystemInterface;

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
    public function load(string $identifier): array
    {
        $this->assertFileExists($identifier);

        $contents = $this->filesystem->getFileContents($identifier);

        return array_merge(
            ['path' => dirname($identifier)],
            json_decode($contents, true)
        );
    }

    /**
     * @param string $path
     */
    private function assertFileExists(string $path): void
    {
        if (!$this->filesystem->exists($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Given file "%s" does not exist!',
                $path
            ));
        }
    }
}
