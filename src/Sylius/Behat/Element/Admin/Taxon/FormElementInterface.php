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

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function getCode(): string;

    public function specifyCode(string $code): void;

    public function isCodeDisabled(): bool;

    public function nameIt(string $name, string $localeCode): void;

    public function slugIt(string $slug, string $localeCode): void;

    public function generateSlug(string $localeCode): void;

    public function describeItAs(string $description, string $localeCode): void;

    public function getParent(): string;

    public function chooseParent(TaxonInterface $taxon): void;

    public function removeCurrentParent(): void;

    public function getTranslationFieldValue(string $element, string $localeCode): string;

    public function enable(): void;

    public function disable(): void;

    public function isEnabled(): bool;
}
