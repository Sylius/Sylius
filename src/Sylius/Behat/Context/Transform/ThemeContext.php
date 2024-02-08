<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

final class ThemeContext implements Context
{
    public function __construct(private ThemeRepositoryInterface $themeRepository)
    {
    }

    /**
     * @Transform /^"([^"]+)" theme$/
     * @Transform /^theme "([^"]+)"$/
     * @Transform :theme
     */
    public function getThemeByThemeName($themeName)
    {
        return $this->themeRepository->findOneByName($themeName);
    }
}
