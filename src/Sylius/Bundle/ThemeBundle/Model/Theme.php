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
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
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
     * @var Collection|ThemeScreenshot[]
     */
    protected $screenshots;

    public function __construct($name, $path)
    {
        $this->id = substr(md5($name), 0, 8);

        $this->name = $name;
        $this->path = $path;

        $this->authors = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->screenshots = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ?: $this->name ?: '';
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
    public function getPath()
    {
        return $this->path;
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
    public function addScreenshot(ThemeScreenshot $themeScreenshot)
    {
        $this->screenshots->add($themeScreenshot);
    }

    /**
     * {@inheritdoc}
     */
    public function removeScreenshot(ThemeScreenshot $themeScreenshot)
    {
        $this->screenshots->removeElement($themeScreenshot);
    }
}
