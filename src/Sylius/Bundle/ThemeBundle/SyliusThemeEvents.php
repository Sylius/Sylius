<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle;

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
final class SyliusThemeEvents
{
    /**
     * The THEME_ADDED event occurs when a new theme is added.
     *
     * @var string
     */
    const THEME_ADDED = 'sylius.theme.added';

    /**
     * The THEME_REMOVED event occurs when the existing theme is removed.
     *
     * @var string
     */
    const THEME_REMOVED = 'sylius.theme.removed';

    /**
     * The THEME_UPDATED event occurs when the existing theme is updated.
     *
     * @var string
     */
    const THEME_UPDATED = 'sylius.theme.updated';
}
