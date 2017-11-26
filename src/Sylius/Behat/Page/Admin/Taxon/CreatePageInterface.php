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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @return int
     */
    public function countTaxons(): int;

    /**
     * @param string $name
     *
     * @return int
     */
    public function countTaxonsByName(string $name): int;

    /**
     * @param string $name
     */
    public function deleteTaxonOnPageByName(string $name): void;

    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeItAs(string $description, string $languageCode): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasTaxonWithName(string $name): bool;

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameIt(string $name, string $languageCode): void;

    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

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
     *
     * @return NodeElement[]
     *
     * @throws ElementNotFoundException
     */
    public function getLeaves(?TaxonInterface $parentTaxon = null);

    /**
     * @param string $locale
     */
    public function activateLanguageTab(string $locale): void;
}
