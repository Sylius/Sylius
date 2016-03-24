<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Synchronizer;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeSynchronizerInterface
{
    /**
     * @param null|ThemeInterface $theme The theme
     *
     * @throws SynchronizationFailedException If synchronization fails
     */
    public function synchronize(ThemeInterface $theme = null);
}
