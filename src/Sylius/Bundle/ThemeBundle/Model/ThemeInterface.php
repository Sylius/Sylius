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
interface ThemeInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string|null
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return ThemeAuthor[]
     */
    public function getAuthors();

    /**
     * @param ThemeAuthor $author
     */
    public function addAuthor(ThemeAuthor $author);

    /**
     * @param ThemeAuthor $author
     */
    public function removeAuthor(ThemeAuthor $author);

    /**
     * @return ThemeInterface[]
     */
    public function getParents();

    /**
     * @param ThemeInterface $theme
     */
    public function addParent(ThemeInterface $theme);

    /**
     * @param ThemeInterface $theme
     */
    public function removeParent(ThemeInterface $theme);

    /**
     * @return ThemeScreenshot[]
     */
    public function getScreenshots();

    /**
     * @param ThemeScreenshot $themeScreenshot
     */
    public function addScreenshot(ThemeScreenshot $themeScreenshot);

    /**
     * @param ThemeScreenshot $themeScreenshot
     */
    public function removeScreenshot(ThemeScreenshot $themeScreenshot);
}
