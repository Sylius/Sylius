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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Theme implements ThemeInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Collection|ThemeAuthor[]
     */
    protected $authors;

    /**
     * @var Collection|ThemeInterface[]
     */
    protected $parents;

    /**
     * @var Collection|string[]
     */
    protected $screenshots;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->screenshots = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
    public function setName($name)
    {
        $this->name = $name;
        $this->code = substr(md5($name), 0, 8);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
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
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthors()
    {
        return $this->authors->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function addAuthor(ThemeAuthor $author)
    {
        $this->authors->add($author);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAuthor(ThemeAuthor $author)
    {
        $this->authors->removeElement($author);
    }

    /**
     * {@inheritdoc}
     */
    public function getParents()
    {
        return $this->parents->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function addParent(ThemeInterface $theme)
    {
        $this->parents->add($theme);
    }

    /**
     * {@inheritdoc}
     */
    public function removeParent(ThemeInterface $theme)
    {
        $this->parents->removeElement($theme);
    }

    /**
     * {@inheritdoc}
     */
    public function getScreenshots()
    {
        return $this->screenshots->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function addScreenshot($path)
    {
        $this->screenshots->add($path);
    }

    /**
     * {@inheritdoc}
     */
    public function removeScreenshot($path)
    {
        $this->screenshots->removeElement($path);
    }
}
