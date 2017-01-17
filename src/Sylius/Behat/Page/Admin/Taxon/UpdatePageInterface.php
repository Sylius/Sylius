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
     * @return bool
     */
    public function isImageCodeDisabled();

    /**
     * @param string $path
     * @param string $code
     */
    public function attachImage($path, $code = null);

    /**
     * @param string $code
     *
     * @return bool
     */
    public function isImageWithCodeDisplayed($code);

    /**
     * @param string $languageCode
     *
     * @return bool
     */
    public function isSlugReadOnly($languageCode = 'en_US');

    /**
     * @param string $code
     */
    public function removeImageWithCode($code);

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
     * @param string $code
     * @param string $path
     */
    public function changeImageWithCode($code, $path);

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
