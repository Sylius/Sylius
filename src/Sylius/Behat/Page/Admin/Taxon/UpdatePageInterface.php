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

namespace Sylius\Behat\Page\Admin\Taxon;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeItAs(string $description, string $languageCode): void;

    /**
     * @param TaxonInterface $taxon
     */
    public function chooseParent(TaxonInterface $taxon): void;

    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameIt(string $name, string $languageCode): void;

    /**
     * @param string $slug
     * @param string $languageCode
     */
    public function specifySlug(string $slug, string $languageCode): void;

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage(string $path, string $type = null): void;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isImageWithTypeDisplayed(string $type): bool;

    /**
     * @param string $languageCode
     *
     * @return bool
     */
    public function isSlugReadonly(string $languageCode = 'en_US'): bool;

    /**
     * @param string $type
     */
    public function removeImageWithType(string $type): void;

    public function removeFirstImage(): void;

    /**
     * @param string $languageCode
     */
    public function enableSlugModification(string $languageCode = 'en_US'): void;

    /**
     * @return int
     */
    public function countImages(): int;

    /**
     * @param string $type
     * @param string $path
     */
    public function changeImageWithType(string $type, string $path): void;

    /**
     * @param string $type
     */
    public function modifyFirstImageType(string $type): void;

    /**
     * @return string
     */
    public function getParent(): string;

    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function getSlug(string $languageCode = 'en_US'): string;

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImage(): string;

    /**
     * @param int $place
     *
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImageAtPlace(int $place): string;

    /**
     * @param string $locale
     */
    public function activateLanguageTab(string $locale): void;
}
