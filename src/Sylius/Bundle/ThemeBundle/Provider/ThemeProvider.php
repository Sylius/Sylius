<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Provider;

use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeProvider implements ThemeProviderInterface
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeFactoryInterface
     */
    private $themeFactory;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeFactoryInterface $themeFactory
     */
    public function __construct(ThemeRepositoryInterface $themeRepository, ThemeFactoryInterface $themeFactory)
    {
        $this->themeRepository = $themeRepository;
        $this->themeFactory = $themeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamed($name)
    {
        $theme = $this->themeRepository->findOneByName($name);
        if (null === $theme) {
            $theme = $this->themeFactory->createNamed($name);
        }

        return $theme;
    }
}
