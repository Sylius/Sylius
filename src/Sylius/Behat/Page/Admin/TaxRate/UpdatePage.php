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

namespace Sylius\Behat\Page\Admin\TaxRate;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use FormAwareTrait;
    use ChecksCodeImmutability;

    public function removeZone(): void
    {
        $this->getElement('field_zone')->setValue('');
    }

    public function isIncludedInPrice(): bool
    {
        return $this->getElement('field_included_in_price')->isChecked();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('field_code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), $this->getDefinedFormElements());
    }
}
