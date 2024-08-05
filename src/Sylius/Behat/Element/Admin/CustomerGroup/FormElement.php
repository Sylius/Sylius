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

namespace Sylius\Behat\Element\Admin\CustomerGroup;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    use ChecksCodeImmutability;

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'name' => '[data-test-name]',
        ]);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }
}
