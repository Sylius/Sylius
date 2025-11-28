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

namespace Sylius\Behat\Element\Admin\ProductAssociationType;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Element\NodeElement;

class FormElement extends BaseFormElement implements FormElementInterface
{
    use ChecksCodeImmutability;

    public function setCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function setName(string $name, string $localeCode): void
    {
        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'name' => '[data-test-name="%locale%"]',
        ]);
    }
}
