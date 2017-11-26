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

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface UpdateConfigurableProductPageInterface extends UpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn(string $name, string $localeCode): void;

    /**
     * @param string $option
     */
    public function isProductOptionChosen(string $option): void;

    /**
     * @return bool
     */
    public function isProductOptionsDisabled(): bool;

    /**
     * @param string $taxonName
     *
     * @return bool
     */
    public function isMainTaxonChosen(string $taxonName): bool;

    /**
     * @param TaxonInterface $taxon
     */
    public function selectMainTaxon(TaxonInterface $taxon): void;

    /**
     * @param string $channelName
     */
    public function checkChannel(string $channelName): void;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isImageWithTypeDisplayed(string $type): bool;

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage(string $path, string $type = null): void;

    /**
     * @param string $type
     * @param string $path
     */
    public function changeImageWithType(string $type, string $path): void;

    /**
     * @param string $type
     */
    public function removeImageWithType(string $type): void;

    public function removeFirstImage(): void;

    /**
     * @param string $type
     */
    public function modifyFirstImageType(string $type): void;

    /**
     * @return int
     */
    public function countImages(): int;
}
