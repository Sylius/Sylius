<?php

namespace Sylius\Bundle\ThemeBundle\Model;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Theme implements ThemeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $logicalName;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $path;

    /**
     * @var ThemeInterface[]
     */
    private $parents = [];

    public function __toString()
    {
        return $this->getLogicalName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogicalName($logicalName)
    {
        $this->logicalName = $logicalName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogicalName()
    {
        return $this->logicalName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setParents(array $parents)
    {
        foreach ($parents as $parent) {
            if ($parent instanceof ThemeInterface) {
                $this->parents[$parent->getLogicalName()] = $parent;
            } else {
                $this->parents[$parent] = null;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentsNames()
    {
        return array_keys($this->parents);
    }

    /**
     * {@inheritdoc}
     */
    public function getHashCode()
    {
        return md5($this->logicalName);
    }
}