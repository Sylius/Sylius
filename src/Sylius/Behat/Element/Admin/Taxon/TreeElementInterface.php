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

namespace Sylius\Behat\Element\Admin\Taxon;

interface TreeElementInterface
{
    public function getTaxonsNames(): array;

    public function countTaxons(): int;

    public function isTaxonOnTheList(string $taxonName): bool;

    public function getFirstTaxonOnTheList(): string;

    public function getLastTaxonOnTheList(): string;

    public function moveUpTaxon(string $name): void;

    public function moveDownTaxon(string $name): void;
}
