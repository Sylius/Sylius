<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Taxon;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeItAs($description, $languageCode);

    /**
     * @param TaxonInterface $taxon
     */
    public function chooseParent(TaxonInterface $taxon);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameIt($name, $languageCode);

    /**
     * @param string $slug
     * @param string $languageCode
     */
    public function specifySlug($slug, $languageCode);

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage($path, $type = null);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isImageWithTypeDisplayed($type);

    /**
     * @param string $languageCode
     *
     * @return bool
     */
    public function isSlugReadonly($languageCode = 'en_US');

    /**
     * @param string $type
     */
    public function removeImageWithType($type);

    public function removeFirstImage();

    /**
     * @param string $languageCode
     */
    public function enableSlugModification($languageCode = 'en_US');

    /**
     * @return int
     */
    public function countImages();

    /**
     * @param string $type
     * @param string $path
     */
    public function changeImageWithType($type, $path);

    /**
     * @param string $type
     */
    public function modifyFirstImageType($type);

    /**
     * @return string
     */
    public function getParent();

    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function getSlug($languageCode = 'en_US');

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImage();

    /**
     * @param int $place
     *
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImageAtPlace($place);

    /**
     * @param string $locale
     */
    public function activateLanguageTab($locale);
}
