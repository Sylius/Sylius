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

use Behat\Mink\Exception\ElementNotFoundException;
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
     * @param string $code
     *
     * @return bool
     */
    public function isImageWithCodeDisplayed($code);

    /**
     * @param string $path
     * @param string $code
     */
    public function attachImage($path, $code = null);

    /**
     * @param string $code
     * @param string $path
     */
    public function changeImageWithCode($code, $path);

    /**
     * @param string $code
     */
    public function removeImageWithCode($code);

    public function removeFirstImage();

    /**
     * @return int
     */
    public function countImages();

    /**
     * @return bool
     */
    public function isImageCodeDisabled();

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImage();
}
