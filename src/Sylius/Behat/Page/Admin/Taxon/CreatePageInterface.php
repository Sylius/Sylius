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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @return int
     */
    public function countTaxons();

    /**
     * @param string $name
     *
     * @return int
     */
    public function countTaxonsByName($name);

    /**
     * @param string $name
     */
    public function deleteTaxonOnPageByName($name);

    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeItAs($description, $languageCode);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasTaxonWithName($name);

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameIt($name, $languageCode);

    /**
     * @param string $code
     */
    public function specifyCode($code);

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
     * @param TaxonInterface|null $parentTaxon
     *
     * @return NodeElement[]
     *
     * @throws ElementNotFoundException
     */
    public function getLeaves(TaxonInterface $parentTaxon = null);

    /**
     * @param string $locale
     */
    public function activateLanguageTab($locale);
}
