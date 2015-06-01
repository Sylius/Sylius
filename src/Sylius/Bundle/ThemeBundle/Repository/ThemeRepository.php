<?php

namespace Sylius\Bundle\ThemeBundle\Repository;

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
            if (!$theme instanceof ThemeInterface) {
                $theme = unserialize($theme);
            }

            $this->themes[$theme->getLogicalName()] = $theme;
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
        return isset($this->themes[$logicalName]) ? $this->themes[$logicalName] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByPath($path)
    {
        $path = realpath($path);

        foreach ($this->themes as $theme) {
            if (false !== strpos($path, $theme->getPath())) {
                return $theme;
            }
        }

        return null;
    }
}