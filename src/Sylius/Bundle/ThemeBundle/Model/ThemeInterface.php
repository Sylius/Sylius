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

interface ThemeInterface
{
    public function getName(): string;

    public function getPath(): string;

    public function getTitle(): ?string;

    public function setTitle(?string $title): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    /**
     * @return array|ThemeAuthor[]
     */
    public function getAuthors(): array;

    public function addAuthor(ThemeAuthor $author): void;

    public function removeAuthor(ThemeAuthor $author): void;

    /**
     * @return array|ThemeInterface[]
     */
    public function getParents(): array;

    /**
     * @param ThemeInterface $theme
     */
    public function addParent(self $theme): void;

    /**
     * @param ThemeInterface $theme
     */
    public function removeParent(self $theme): void;

    /**
     * @return array|ThemeScreenshot[]
     */
    public function getScreenshots(): array;

    public function addScreenshot(ThemeScreenshot $screenshot): void;

    public function removeScreenshot(ThemeScreenshot $screenshot): void;
}
