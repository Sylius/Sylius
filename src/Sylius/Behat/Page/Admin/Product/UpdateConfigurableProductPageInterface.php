<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UpdateConfigurableProductPageInterface extends UpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn($name, $localeCode);

    /**
     * @param string $option
     */
    public function isProductOptionChosen($option);

    /**
     * @return bool
     */
    public function isProductOptionsDisabled();

    /**
     * @param string $taxonName
     *
     * @return bool
     */
    public function isMainTaxonChosen($taxonName);

    /**
     * @param TaxonInterface $taxon
     */
    public function selectMainTaxon(TaxonInterface $taxon);

    /**
     * @param string $channelName
     */
    public function checkChannel($channelName);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isImageWithTypeDisplayed($type);

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage($path, $type = null);

    /**
     * @param string $type
     * @param string $path
     */
    public function changeImageWithType($type, $path);

    /**
     * @param string $type
     */
    public function removeImageWithType($type);

    public function removeFirstImage();

    /**
     * @param string $type
     */
    public function modifyFirstImageType($type);

    /**
     * @return int
     */
    public function countImages();
}
