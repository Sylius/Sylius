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
    public function describeItAs(string $description, string $languageCode);

    public function chooseParent(TaxonInterface $taxon);

    public function isCodeDisabled(): bool;

    public function nameIt(string $name, string $languageCode);

    public function specifySlug(string $slug, string $languageCode);

    /**
     * @param string $type
     */
    public function attachImage(string $path, string $type = null);

    public function isImageWithTypeDisplayed(string $type): bool;

    public function isSlugReadonly(string $languageCode = 'en_US'): bool;

    public function removeImageWithType(string $type);

    public function removeFirstImage();

    public function enableSlugModification(string $languageCode = 'en_US');

    public function countImages(): int;

    public function changeImageWithType(string $type, string $path);

    public function modifyFirstImageType(string $type);

    public function getParent(): string;

    public function getSlug(string $languageCode = 'en_US'): string;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImage(): string;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImageAtPlace(int $place): string;

    public function activateLanguageTab(string $locale);
}
