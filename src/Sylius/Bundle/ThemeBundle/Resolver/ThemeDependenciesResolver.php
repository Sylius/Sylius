<?php

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
    public function resolveDependencies(ThemeInterface $theme)
    {
        $parents = [];
        $parentsNames = $theme->getParentsNames();
        foreach ($parentsNames as $parentName) {
            $lastException = null;

            try {
                if (null !== $parent = $this->themeRepository->findByLogicalName($parentName)) {
                    $this->resolveDependencies($parent);
                    $parents[] = $parent;
                    continue;
                }
            } catch (\InvalidArgumentException $e) {
                $lastException = $e;
            }

            throw new \InvalidArgumentException(sprintf(
                'Theme "%s" not found (required by theme "%s")!', $parentName, $theme->getLogicalName()
            ), 0, $lastException);
        }

        $theme->setParents($parents);
    }
}