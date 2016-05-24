<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Uploader;

use Gaufrette\Filesystem;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class GeneratorBasedPathProvider implements PathProviderInterface
{
    /**
     * @var PathGeneratorInterface
     */
    private $pathGenerator;

    /**
     * @param PathGeneratorInterface $pathGenerator
     */
    public function __construct(PathGeneratorInterface $pathGenerator)
    {
        $this->pathGenerator = $pathGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(Filesystem $filesystem, \SplFileInfo $file)
    {
        $uniquePath = null;
        foreach ($this->pathGenerator->generate($file) as $generatedPath) {
            if (!$filesystem->has($generatedPath)) {
                $uniquePath = $generatedPath;

                break;
            }
        }

        if (null === $uniquePath) {
            throw new \RuntimeException('Could not generate path for given file');
        }

        return $uniquePath;
    }
}
