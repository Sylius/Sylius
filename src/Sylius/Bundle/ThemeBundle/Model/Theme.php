<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @var string[]
     */
    private $parentsNames = [];

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
    public function setParentsNames(array $parentsNames)
    {
        $this->parentsNames = $parentsNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentsNames()
    {
        return $this->parentsNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getHashCode()
    {
        return md5($this->logicalName);
    }
}