<?php

namespace Sylius\Bundle\ThemeBundle\Locator;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ResourceLocatorInterface
{
    /**
     * @param string $resourceName
     * @param ThemeInterface[] $themes
     *
     * @return string|null
     */
    public function locateResource($resourceName, array $themes = []);
}