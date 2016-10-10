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
final class ResourceNotFoundException extends \RuntimeException
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
            $theme->getName()
        ));
    }
}
