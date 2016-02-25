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

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     */
    public function setPath($path);

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
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

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
     * Should match /^[a-zA-Z0-9]{6,32}$/
     *
     * @return string
     */
    public function getCode();
}
