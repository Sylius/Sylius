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
     * @var ThemeAuthor[]
     */
    protected $authors = [];

    /**
     * @var ThemeInterface[]
     */
    protected $parents = [];

    /**
     * @var ThemeScreenshot[]
     */
    protected $screenshots = [];

    /**
     * @param string $name
     * @param string $path
     */
    public function __construct($name, $path)
    {
        $this->assertNameIsValid($name);

        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ?: $this->name;
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
        return $this->authors;
    }

    /**
     * {@inheritdoc}
     */
    public function addAuthor(ThemeAuthor $author)
    {
        $this->authors[] = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAuthor(ThemeAuthor $author)
    {
        $this->authors = array_filter($this->authors, function ($currentAuthor) use ($author) {
            return $currentAuthor !== $author;
        });
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
    public function addParent(ThemeInterface $theme)
    {
        $this->parents[] = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function removeParent(ThemeInterface $theme)
    {
        $this->parents = array_filter($this->parents, function ($currentTheme) use ($theme) {
            return $currentTheme !== $theme;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getScreenshots()
    {
        return $this->screenshots;
    }

    /**
     * {@inheritdoc}
     */
    public function addScreenshot(ThemeScreenshot $screenshot)
    {
        $this->screenshots[] = $screenshot;
    }

    /**
     * {@inheritdoc}
     */
    public function removeScreenshot(ThemeScreenshot $screenshot)
    {
        $this->screenshots = array_filter($this->screenshots, function ($currentScreenshot) use ($screenshot) {
            return $currentScreenshot !== $screenshot;
        });
    }

    /**
     * @param string $name
     */
    private function assertNameIsValid($name)
    {
        $pattern = '/^[a-z\-]+\/[a-z\-]+$/i';
        if (false === (bool) preg_match($pattern, $name)) {
            throw new \InvalidArgumentException(sprintf(
                'Given name "%s" does not match regular expression "%s".',
                $name,
                $pattern
            ));
        }
    }
}
