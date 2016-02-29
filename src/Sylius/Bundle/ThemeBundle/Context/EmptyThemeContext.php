<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Context;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class EmptyThemeContext implements ThemeContextInterface
{
    /**
     * {@inheritdoc}
     *
     * @return null
     */
    public function getTheme()
    {
        return null;
    }
}
