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

namespace Sylius\Behat\Element\Admin\TaxCategory;

use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    public function setCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function isCodeDisabled(): bool
    {
        return $this->getElement('code')->hasAttribute('disabled');
    }

    public function setName(string $name): void
    {
        $this->getElement('name')->setValue($name);
    }

    public function setDescription(string $description): void
    {
        $this->getElement('description')->setValue($description);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'description' => '[data-test-description]',
            'name' => '[data-test-name]',
        ]);
    }
}
