<?php

namespace Sylius\Bundle\ThemeBundle\Repository;

use Sylius\Bundle\ThemeBundle\Exception\InvalidArgumentException;
use Sylius\Bundle\ThemeBundle\Finder\ThemeFinderInterface;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeRepository implements ThemeRepositoryInterface
{
    /**
     * @var ThemeInterface[]
     */
    private $themes = [];

    /**
     * @param ThemeInterface[] $themes (as objects or as serialized objects)
     */
    public function __construct(array $themes = [])
    {
        foreach ($themes as $theme) {
            if ($theme instanceof ThemeInterface) {
                $this->themes[] = $theme;
            } else {
                $this->themes[] = unserialize($theme);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function findByLogicalName($logicalName)
    {
        foreach ($this->themes as $theme) {
            if ($logicalName === $theme->getLogicalName()) {
                return $theme;
            }
        }

        return null;
    }
}