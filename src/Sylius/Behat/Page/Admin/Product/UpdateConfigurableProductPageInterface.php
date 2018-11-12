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
    public function isCodeDisabled(): bool;

    public function nameItIn(string $name, string $localeCode);

    public function isProductOptionChosen(string $option);

    public function isProductOptionsDisabled(): bool;

    public function isMainTaxonChosen(string $taxonName): bool;

    public function selectMainTaxon(TaxonInterface $taxon);

    public function checkChannel(string $channelName);

    public function isImageWithTypeDisplayed(string $type): bool;

    /**
     * @param string $type
     */
    public function attachImage(string $path, string $type = null);

    public function changeImageWithType(string $type, string $path);

    public function removeImageWithType(string $type);

    public function removeFirstImage();

    public function modifyFirstImageType(string $type);

    public function countImages(): int;
}
