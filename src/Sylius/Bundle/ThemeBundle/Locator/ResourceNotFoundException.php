<?php

namespace Sylius\Bundle\ThemeBundle\Locator;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ResourceNotFoundException extends \RuntimeException
{
    /**
     * @param string $resourceName
     * @param ThemeInterface $theme
     */
    public function __construct($resourceName, ThemeInterface $theme)
    {
        parent::__construct(sprintf(
            'Could not find resource "%s" using theme "%s".',
            $resourceName,
            $theme->getSlug()
        ));
    }
}
