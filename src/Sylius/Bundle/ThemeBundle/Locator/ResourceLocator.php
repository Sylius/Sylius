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
final class ResourceLocator implements ResourceLocatorInterface
{
    /**
     * @var ResourceLocatorInterface
     */
    private $applicationResourceLocator;

    /**
     * @var ResourceLocatorInterface
     */
    private $bundleResourceLocator;

    /**
     * @param ResourceLocatorInterface $applicationResourceLocator
     * @param ResourceLocatorInterface $bundleResourceLocator
     */
    public function __construct(
        ResourceLocatorInterface $applicationResourceLocator,
        ResourceLocatorInterface $bundleResourceLocator
    ) {
        $this->applicationResourceLocator = $applicationResourceLocator;
        $this->bundleResourceLocator = $bundleResourceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource($resourcePath, ThemeInterface $theme)
    {
        if (0 === strpos($resourcePath, '@')) {
            return $this->bundleResourceLocator->locateResource($resourcePath, $theme);
        }

        return $this->applicationResourceLocator->locateResource($resourcePath, $theme);
    }
}
