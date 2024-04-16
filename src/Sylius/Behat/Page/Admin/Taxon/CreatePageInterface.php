<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
    public function countTaxons(): int;

    public function countTaxonsByName(string $name): int;

    public function deleteTaxonOnPageByName(string $name): void;

    public function describeItAs(string $description, string $languageCode): void;

    public function hasTaxonWithName(string $name): bool;

    public function nameIt(string $name, string $languageCode): void;

    public function specifyCode(string $code): void;

    public function specifySlug(string $slug, string $languageCode): void;

    public function attachImage(string $path, ?string $type = null): void;

    /**
     * @return NodeElement[]
     *
     * @throws ElementNotFoundException
     */
    public function getLeaves(?TaxonInterface $parentTaxon = null): array;

    public function activateLanguageTab(string $locale): void;

    public function moveUpTaxon(string $name): void;

    public function moveDownTaxon(string $name): void;

    public function getFirstTaxonOnTheList(): string;

    public function getLastTaxonOnTheList(): string;
}
