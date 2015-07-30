<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Resolver;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeDependenciesResolver implements ThemeDependenciesResolverInterface
{
    /**
     * @var ThemeRepositoryInterface
     */
    protected $themeRepository;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(ThemeRepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(ThemeInterface $theme)
    {
        $parents = [];
        $parentsNames = $theme->getParentsNames();
        foreach ($parentsNames as $parentName) {
            $parent = $this->themeRepository->findByLogicalName($parentName);

            if (null === $parent) {
                throw new \InvalidArgumentException(sprintf(
                    'Theme "%s" not found (required by theme "%s")!', $parentName, $theme->getLogicalName()
                ), 0);
            }

            $parents = array_merge($parents, [$parent], $this->getDependencies($parent));
        }

        return $parents;
    }
}