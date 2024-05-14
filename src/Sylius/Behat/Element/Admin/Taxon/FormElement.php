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

use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    public function getCode(): string
    {
        return $this->getElement('code')->getValue();
    }

    public function nameIt(string $name, string $localeCode): void
    {
        $this->getElement('name', ['%locale_code%' => $localeCode])->setValue($name);
    }

    public function slugIt(string $slug, string $localeCode): void
    {
        $this->getElement('slug', ['%locale_code%' => $localeCode])->setValue($slug);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'form' => '[data-live-name-value="sylius_admin:taxon:form"]',
            'name' => '[name="taxon[translations][%locale_code%][name]"]',
            'slug' => '[name="taxon[translations][%locale_code%][slug]"]',
        ]);
    }
}
