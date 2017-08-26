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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeContext implements Context
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(ThemeRepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
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
