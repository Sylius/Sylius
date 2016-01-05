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
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ApplicationResourceLocator implements ResourceLocatorInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource($resourceName, ThemeInterface $theme)
    {
        $path = sprintf('%s/%s', $theme->getPath(), $resourceName);
        if (!$this->filesystem->exists($path)) {
            throw new ResourceNotFoundException($resourceName, $theme);
        }

        return $path;
    }
}
