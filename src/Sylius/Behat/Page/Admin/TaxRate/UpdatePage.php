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
    use ChecksCodeImmutability;

    public function removeZone(): void
    {
        $this->getDocument()->selectFieldOption('Zone', 'Select');
    }

    public function isIncludedInPrice(): bool
    {
        return (bool) $this->getElement('included_in_price')->getValue();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_tax_rate_amount',
            'calculator' => '#sylius_tax_rate_calculator',
            'category' => '#sylius_tax_rate_category',
            'code' => '#sylius_tax_rate_code',
            'name' => '#sylius_tax_rate_name',
            'zone' => '#sylius_tax_rate_zone',
            'included_in_price' => '#sylius_tax_rate_includedInPrice',
        ]);
    }
}
