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

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

final class SettableThemeContext implements ThemeContextInterface
{
    /**
     * @var ThemeInterface
     */
    private $theme;

    /**
     * {@inheritdoc}
     */
    public function setTheme(ThemeInterface $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme(): ?ThemeInterface
    {
        return $this->theme;
    }
}
