<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Model;

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
     * @var array|ThemeAuthor[]
     */
    protected $authors = [];

    /**
     * @var array|ThemeInterface[]
     */
    protected $parents = [];

    /**
     * @var array|ThemeScreenshot[]
     */
    protected $screenshots = [];

    /**
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path)
    {
        $this->assertNameIsValid($name);

        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) ($this->title ?: $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthors(): array
    {
        return $this->authors;
    }

    /**
     * {@inheritdoc}
     */
    public function addAuthor(ThemeAuthor $author): void
    {
        $this->authors[] = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAuthor(ThemeAuthor $author): void
    {
        $this->authors = array_filter($this->authors, function ($currentAuthor) use ($author) {
            return $currentAuthor !== $author;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    /**
     * {@inheritdoc}
     */
    public function addParent(ThemeInterface $theme): void
    {
        $this->parents[] = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function removeParent(ThemeInterface $theme): void
    {
        $this->parents = array_filter($this->parents, function ($currentTheme) use ($theme) {
            return $currentTheme !== $theme;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getScreenshots(): array
    {
        return $this->screenshots;
    }

    /**
     * {@inheritdoc}
     */
    public function addScreenshot(ThemeScreenshot $screenshot): void
    {
        $this->screenshots[] = $screenshot;
    }

    /**
     * {@inheritdoc}
     */
    public function removeScreenshot(ThemeScreenshot $screenshot): void
    {
        $this->screenshots = array_filter($this->screenshots, function ($currentScreenshot) use ($screenshot) {
            return $currentScreenshot !== $screenshot;
        });
    }

    /**
     * @param string $name
     */
    private function assertNameIsValid(string $name): void
    {
        $pattern = '/^[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-]+$/';
        if (false === (bool) preg_match($pattern, $name)) {
            throw new \InvalidArgumentException(sprintf(
                'Given name "%s" does not match regular expression "%s".',
                $name,
                $pattern
            ));
        }
    }
}
