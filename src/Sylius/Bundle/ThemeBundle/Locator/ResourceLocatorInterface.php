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

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ResourceLocatorInterface
{
    /**
     * @param string $resourceName
     * @param ThemeInterface $theme
     *
     * @return string
     *
     * @throws ResourceNotFoundException
     */
    public function locateResource($resourceName, ThemeInterface $theme);
}
